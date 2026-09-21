<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'creator'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('purchase_no', 'like', "%{$s}%")
                  ->orWhere('supplier_invoice_no', 'like', "%{$s}%")
                  ->orWhereHas('supplier', function ($sup) use ($s) {
                      $sup->where('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::all();

        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::where('status', 'active')->get();

        return view('admin.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
        ]);

        try {
            $purchase = $this->purchaseService->createPurchase($request->all());
            return redirect()->route('admin.purchases.show', $purchase)->with('success', "Purchase {$purchase->purchase_no} recorded successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating purchase: ' . $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'items.serials', 'payments.creator', 'creator']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function addPayment(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $purchase->due_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'supplier_id' => $purchase->supplier_id,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_no' => $request->reference_no,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        $purchase->paid_amount += $request->amount;
        $purchase->due_amount = max(0.00, $purchase->grand_total - $purchase->paid_amount);
        $purchase->payment_status = $purchase->due_amount <= 0 ? 'paid' : 'partial';
        $purchase->save();

        // Update supplier balance
        $supplier = $purchase->supplier;
        $supplier->current_balance = max(0.00, $supplier->current_balance - $request->amount);
        $supplier->save();

        return back()->with('success', 'Payment of ₹' . number_format($request->amount, 2) . ' recorded successfully.');
    }
}
