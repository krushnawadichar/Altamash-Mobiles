<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $returns = SaleReturn::with(['sale', 'customer', 'creator', 'items.product'])->latest()->paginate(15);
        return view('admin.sales.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->filled('invoice_no')) {
            $sale = Sale::with(['items.product', 'items.serial', 'customer'])
                ->where('invoice_no', $request->invoice_no)
                ->first();
        }

        return view('admin.sales.returns.create', compact('sale'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $sale = Sale::findOrFail($request->sale_id);

            // Generate return number
            $year = date('Y');
            $last = SaleReturn::where('return_no', 'like', "RET-{$year}-%")->orderBy('id', 'desc')->first();
            $next = 1;
            if ($last) {
                $parts = explode('-', $last->return_no);
                if (count($parts) === 3) {
                    $next = intval($parts[2]) + 1;
                }
            }
            $returnNo = sprintf("RET-%s-%06d", $year, $next);

            $totalReturnAmount = 0;

            $saleReturn = SaleReturn::create([
                'return_no' => $returnNo,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'return_date' => $request->return_date,
                'total_amount' => 0,
                'refund_amount' => floatval($request->refund_amount ?? 0),
                'refund_method' => $request->refund_method ?? 'cash',
                'reason' => $request->reason,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $itemData) {
                if (empty($itemData['selected'])) {
                    continue;
                }

                $saleItem = SaleItem::findOrFail($itemData['sale_item_id']);
                $qty = min(intval($itemData['quantity']), $saleItem->quantity);
                $unitPrice = $saleItem->unit_price;
                $itemSubtotal = $unitPrice * $qty;
                $totalReturnAmount += $itemSubtotal;

                SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'product_serial_id' => $saleItem->product_serial_id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $itemSubtotal,
                ]);

                // Restore stock
                $this->inventoryService->recordMovement(
                    product: $saleItem->product,
                    transactionType: 'SALE_RETURN',
                    quantityChange: $qty,
                    referenceId: $saleReturn->id,
                    referenceType: 'SaleReturn',
                    serial: $saleItem->serial,
                    unitCost: $saleItem->unit_cost,
                    notes: "Sale Return: {$saleReturn->return_no} against Invoice: {$sale->invoice_no}"
                );

                // If mobile IMEI, mark available again
                if ($saleItem->serial) {
                    $this->inventoryService->markImeiAvailable($saleItem->serial);
                }
            }

            $saleReturn->total_amount = $totalReturnAmount;
            $saleReturn->save();

            // Adjust customer current balance if applicable
            if ($sale->customer_id) {
                $customer = $sale->customer;
                $customer->current_balance = max(0.00, $customer->current_balance - $totalReturnAmount);
                $customer->save();
            }

            ActivityLog::log('SALE_RETURN', 'sales', $saleReturn->id, "Processed return {$saleReturn->return_no} for Invoice {$sale->invoice_no}, Total: ₹" . number_format($totalReturnAmount, 2));

            return redirect()->route('admin.sales.returns.index')->with('success', "Sale Return {$saleReturn->return_no} processed successfully. Stock & IMEIs updated.");
        });
    }
}
