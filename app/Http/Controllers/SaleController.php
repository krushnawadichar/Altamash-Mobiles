<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'creator'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_mobile', 'like', "%{$s}%")
                  ->orWhereHas('items', function ($item) use ($s) {
                      $item->where('imei', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('sale_date', $request->date);
        }

        $sales = $query->paginate(15)->withQueryString();

        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.product.brand', 'items.serial', 'payments.creator', 'creator', 'returns.items']);
        return view('admin.sales.show', compact('sale'));
    }

    public function addPayment(Request $request, Sale $sale)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $sale->due_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_ref' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        SalePayment::create([
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_ref' => $request->transaction_ref,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        $sale->paid_amount += $request->amount;
        $sale->due_amount = max(0.00, $sale->grand_total - $sale->paid_amount);
        $sale->payment_status = $sale->due_amount <= 0 ? 'paid' : 'partial';
        $sale->save();

        if ($sale->customer_id) {
            $customer = $sale->customer;
            $customer->current_balance = max(0.00, $customer->current_balance - $request->amount);
            $customer->save();
        }

        ActivityLog::log('PAYMENT_RECEIVED', 'sales', $sale->id, "Received payment of ₹" . number_format($request->amount, 2) . " for Invoice {$sale->invoice_no}");

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function cancel(Request $request, Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'This sale is already cancelled.');
        }

        DB::transaction(function () use ($sale, $request) {
            foreach ($sale->items as $item) {
                // Restore stock
                $this->inventoryService->recordMovement(
                    product: $item->product,
                    transactionType: 'SALE_RETURN',
                    quantityChange: $item->quantity,
                    referenceId: $sale->id,
                    referenceType: 'SaleCancellation',
                    serial: $item->serial,
                    unitCost: $item->unit_cost,
                    notes: "Cancellation of Sale {$sale->invoice_no}"
                );

                // Revert mobile IMEI to available
                if ($item->serial) {
                    $this->inventoryService->markImeiAvailable($item->serial);
                }
            }

            // Adjust customer balance if due amount was outstanding
            if ($sale->customer_id && $sale->due_amount > 0) {
                $customer = $sale->customer;
                $customer->current_balance = max(0.00, $customer->current_balance - $sale->due_amount);
                $customer->save();
            }

            $sale->status = 'cancelled';
            $sale->notes = ($sale->notes ? $sale->notes . "\n" : '') . "Cancelled on " . now()->toDateTimeString() . " Reason: " . $request->input('reason', 'N/A');
            $sale->save();

            ActivityLog::log('SALE_CANCELLED', 'sales', $sale->id, "Cancelled sale {$sale->invoice_no}. Stock and IMEIs restored.");
        });

        return back()->with('success', "Sale {$sale->invoice_no} has been cancelled and stock has been restored.");
    }
}
