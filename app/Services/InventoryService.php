<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Record stock movement and update product stock
     */
    public function recordMovement(
        Product $product,
        string $transactionType,
        int $quantityChange, // positive for stock IN, negative for stock OUT
        ?int $referenceId = null,
        ?string $referenceType = null,
        ?ProductSerial $serial = null,
        float $unitCost = 0.0,
        ?string $notes = null
    ): InventoryTransaction {
        $beforeStock = $product->current_stock;
        $afterStock = $beforeStock + $quantityChange;

        if ($afterStock < 0 && $quantityChange < 0) {
            throw new Exception("Insufficient stock for product: {$product->name}. Current: {$beforeStock}, Requested deduction: " . abs($quantityChange));
        }

        $product->current_stock = $afterStock;
        $product->save();

        return InventoryTransaction::create([
            'product_id' => $product->id,
            'product_serial_id' => $serial?->id,
            'transaction_type' => $transactionType,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'quantity' => $quantityChange,
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'unit_cost' => $unitCost ?: $product->purchase_price,
            'notes' => $notes,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Mark mobile IMEI as Sold
     */
    public function markImeiSold(ProductSerial $serial, int $saleItemId, float $sellingPrice = 0): void
    {
        if ($serial->status === 'sold') {
            throw new Exception("Mobile IMEI: {$serial->imei_1} is already sold and cannot be sold again.");
        }

        $serial->status = 'sold';
        $serial->sale_item_id = $saleItemId;
        $serial->selling_price = $sellingPrice ?: $serial->product->selling_price;
        $serial->sold_at = now();
        $serial->save();
    }

    /**
     * Mark mobile IMEI as Available (e.g. on return or cancellation)
     */
    public function markImeiAvailable(ProductSerial $serial): void
    {
        $serial->status = 'available';
        $serial->sale_item_id = null;
        $serial->sold_at = null;
        $serial->save();
    }
}
