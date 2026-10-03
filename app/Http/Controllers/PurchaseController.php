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
        return view('admin.purchases.index', compact('purchases'));
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
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'prices' => 'required|array',
            'quantities' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $payment_status = 'unpaid';
            if ($request->paid_amount >= $request->grand_total) {
                $payment_status = 'paid';
            } elseif ($request->paid_amount > 0) {
                $payment_status = 'partial';
            }

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'purchase_date' => $request->purchase_date,
                'subtotal' => $request->subtotal,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'grand_total' => $request->grand_total,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'payment_status' => $payment_status,
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            foreach ($request->products as $index => $productId) {
                $price = $request->prices[$index];
                $qty = $request->quantities[$index];
                $subtotal = $price * $qty;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'subtotal' => $subtotal
                ]);

                // Update product stock
                $product = Product::find($productId);
                $prevStock = $product->stock_quantity;
                $newStock = $prevStock + $qty;
                
                $product->update([
                    'stock_quantity' => $newStock,
                    'purchase_price' => $price // Update cost price
                ]);

                // Log Transaction
                InventoryTransaction::create([
                    'product_id' => $productId,
                    'transaction_type' => 'purchase',
                    'quantity' => $qty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'reference' => 'Purchase #' . $purchase->id,
                    'user_id' => auth()->id()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.purchases.index')->with('success', 'Purchase recorded and inventory updated.');

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
}
