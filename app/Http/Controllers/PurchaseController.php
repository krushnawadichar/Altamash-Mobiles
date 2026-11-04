<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('supplier')->latest()->get();
        $totalPurchases = (float)Purchase::sum('grand_total');
        $totalPaid = (float)Purchase::sum('paid_amount');
        $totalDue = (float)Purchase::sum('due_amount');
        $totalCount = Purchase::count();

        return view('admin.purchases.index', compact('purchases', 'totalPurchases', 'totalPaid', 'totalDue', 'totalCount'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 1)->get();
        $products = Product::where('status', 1)->get();
        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*' => 'required|exists:products,id',
            'prices' => 'required|array',
            'quantities' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals reliably on backend
            $subtotal = 0;
            foreach ($request->products as $index => $productId) {
                $p = (float)($request->prices[$index] ?? 0);
                $q = (int)($request->quantities[$index] ?? 1);
                $subtotal += ($p * $q);
            }

            $discount = max(0, (float)$request->input('discount', 0));
            $tax = max(0, (float)$request->input('tax', 0));
            $grand_total = max(0, ($subtotal - $discount) + $tax);

            $paid_amount = max(0, (float)$request->input('paid_amount', 0));
            $effective_paid = min($grand_total, $paid_amount);
            $due_amount = max(0, $grand_total - $effective_paid);

            $payment_status = 'unpaid';
            if ($due_amount <= 0 && $grand_total > 0) {
                $payment_status = 'paid';
            } elseif ($effective_paid > 0) {
                $payment_status = 'partial';
            }

            $invoiceNumber = $request->filled('invoice_number') 
                ? trim($request->invoice_number) 
                : 'PUR-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $invoiceNumber,
                'purchase_date' => $request->purchase_date,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'grand_total' => $grand_total,
                'paid_amount' => $effective_paid,
                'due_amount' => $due_amount,
                'payment_status' => $payment_status,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            foreach ($request->products as $index => $productId) {
                $price = (float)$request->prices[$index];
                $qty = (int)$request->quantities[$index];
                $itemSubtotal = $price * $qty;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'subtotal' => $itemSubtotal
                ]);

                // Update product stock and purchase price
                $product = Product::find($productId);
                if ($product) {
                    $prevStock = $product->stock_quantity;
                    $newStock = $prevStock + $qty;
                    
                    $product->update([
                        'stock_quantity' => $newStock,
                        'purchase_price' => $price
                    ]);

                    // Log Transaction
                    InventoryTransaction::create([
                        'product_id' => $productId,
                        'transaction_type' => 'purchase',
                        'quantity' => $qty,
                        'previous_stock' => $prevStock,
                        'new_stock' => $newStock,
                        'reference' => 'Purchase Bill #' . $purchase->invoice_number,
                        'user_id' => auth()->id()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.purchases.show', $purchase)->with('success', 'Purchase recorded and inventory updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording purchase: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'creator']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function print(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'creator']);
        return view('admin.purchases.print', compact('purchase'));
    }

    public function recordPayment(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $paidNow = (float)$request->amount;
        if ($paidNow > (float)$purchase->due_amount) {
            return back()->withErrors([
                'amount' => 'Paid amount cannot exceed the pending due amount (₹' . number_format($purchase->due_amount, 2) . ')'
            ])->with('error', 'Paid amount cannot exceed the pending due amount (₹' . number_format($purchase->due_amount, 2) . ')')->withInput();
        }

        $newPaid = (float)$purchase->paid_amount + $paidNow;
        $newDue = max(0, (float)$purchase->grand_total - $newPaid);
        $newStatus = $newDue <= 0 ? 'paid' : 'partial';

        $refValue = $request->payment_reference ?: $request->reference_number;
        $noteValue = $request->payment_notes ?: $request->notes;

        $stamp = now()->format('d M Y, h:i A');
        $user = auth()->user()?->name ?? 'Admin';
        $refText = !empty($refValue) ? ' [Ref/Txn: ' . $refValue . ']' : '';
        $noteText = !empty($noteValue) ? ' (' . $noteValue . ')' : '';
        $paymentLog = "[{$stamp}] Paid ₹" . number_format($paidNow, 2) . " to supplier via {$request->payment_method}{$refText}{$noteText} by {$user}";

        $existingNotes = $purchase->notes ? $purchase->notes . "\n" . $paymentLog : $paymentLog;

        $purchase->update([
            'paid_amount' => $newPaid,
            'due_amount' => $newDue,
            'payment_status' => $newStatus,
            'notes' => $existingNotes,
        ]);

        return back()->with('success', 'Payment of ₹' . number_format($paidNow, 2) . ' to ' . ($purchase->supplier->name ?? 'supplier') . ' recorded successfully! Due balance updated.');
    }
}
