<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $adjustments = StockAdjustment::with(['items.product', 'creator'])->latest()->paginate(15);
        return view('admin.inventory.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();
        return view('admin.inventory.adjustments.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|in:damaged,lost,found,correction,manual',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.type' => 'required|in:increase,decrease',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $year = date('Y');
            $last = StockAdjustment::where('adjustment_no', 'like', "ADJ-{$year}-%")->orderBy('id', 'desc')->first();
            $next = 1;
            if ($last) {
                $parts = explode('-', $last->adjustment_no);
                if (count($parts) === 3) {
                    $next = intval($parts[2]) + 1;
                }
            }
            $adjNo = sprintf("ADJ-%s-%06d", $year, $next);

            $adjustment = StockAdjustment::create([
                'adjustment_no' => $adjNo,
                'date' => $request->date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            foreach ($request->items as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $type = $itemData['type'];
                $qty = intval($itemData['quantity']);
                $qtyChange = $type === 'increase' ? $qty : -$qty;
                $transType = $type === 'increase' ? 'STOCK_ADJUSTMENT_IN' : 'STOCK_ADJUSTMENT_OUT';

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                    'type' => $type,
                    'quantity' => $qty,
                    'unit_cost' => $product->purchase_price,
                ]);

                $this->inventoryService->recordMovement(
                    product: $product,
                    transactionType: $transType,
                    quantityChange: $qtyChange,
                    referenceId: $adjustment->id,
                    referenceType: 'StockAdjustment',
                    serial: null,
                    unitCost: $product->purchase_price,
                    notes: "Stock Adjustment {$adjustment->adjustment_no} ({$request->reason})"
                );
            }

            ActivityLog::log('STOCK_ADJUSTED', 'inventory', $adjustment->id, "Processed stock adjustment: {$adjustment->adjustment_no} Reason: {$request->reason}");

            return redirect()->route('admin.inventory.adjustments.index')->with('success', "Stock Adjustment {$adjustment->adjustment_no} recorded successfully.");
        });
    }
}
