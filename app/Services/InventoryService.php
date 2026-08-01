<?php

namespace App\Services;

use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    protected $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function getAllInventory()
    {
        return $this->inventoryRepository->all();
    }

    public function getInventoryByProduct($productId, $type = 'App\Models\Product')
    {
        return $this->inventoryRepository->findByProduct($productId, $type);
    }

    public function getLowStockItems()
    {
        return $this->inventoryRepository->getLowStock();
    }

    public function createInventory(array $data)
    {
        $data['created_by'] = auth()->id();
        return $this->inventoryRepository->create($data);
    }

    public function stockAdjustment(array $data)
    {
        return DB::transaction(function () use ($data) {
            $inventory = $this->createInventory([
                'inventoriable_id' => $data['item_id'],
                'inventoriable_type' => $data['item_type'],
                'type' => 'adjustment',
                'quantity' => $data['quantity'],
                'price' => $data['price'] ?? 0,
                'total_price' => ($data['price'] ?? 0) * $data['quantity'],
                'remarks' => $data['remarks'] ?? 'Stock adjustment',
            ]);

            // Update the actual product/accessory stock
            if ($data['item_type'] === 'App\Models\Product') {
                $productService = app(ProductService::class);
                $productService->updateStock($data['item_id'], $data['quantity'], 
                    $data['quantity'] >= 0 ? 'add' : 'subtract');
            } else {
                $accessoryService = app(AccessoryService::class);
                $accessoryService->updateStock($data['item_id'], abs($data['quantity']), 
                    $data['quantity'] >= 0 ? 'add' : 'subtract');
            }

            return $inventory;
        });
    }

    public function stockTransfer(array $data)
    {
        // Implementation for stock transfer between products
        return DB::transaction(function () use ($data) {
            // Subtract from source
            $this->stockAdjustment([
                'item_id' => $data['source_id'],
                'item_type' => $data['item_type'],
                'quantity' => -$data['quantity'],
                'price' => $data['price'] ?? 0,
                'remarks' => 'Transfer to ' . ($data['destination_name'] ?? 'another location'),
            ]);

            // Add to destination
            $this->stockAdjustment([
                'item_id' => $data['destination_id'],
                'item_type' => $data['item_type'],
                'quantity' => $data['quantity'],
                'price' => $data['price'] ?? 0,
                'remarks' => 'Transfer from ' . ($data['source_name'] ?? 'another location'),
            ]);

            return true;
        });
    }
}