<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use App\Services\ProductService;
use App\Services\AccessoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    protected $inventoryService;
    protected $productService;
    protected $accessoryService;

    public function __construct(
        InventoryService $inventoryService,
        ProductService $productService,
        AccessoryService $accessoryService
    ) {
        $this->inventoryService = $inventoryService;
        $this->productService = $productService;
        $this->accessoryService = $accessoryService;
    }

    public function index()
    {
        $inventories = $this->inventoryService->getAllInventory();
        $lowStockProducts = $this->productService->getLowStockProducts();
        $outOfStockProducts = $this->productService->getOutOfStockProducts();
        $lowStockAccessories = $this->accessoryService->getLowStockAccessories();
        $outOfStockAccessories = $this->accessoryService->getOutOfStockAccessories();
        $products = $this->productService->getAllProducts();
        $accessories = $this->accessoryService->getAllAccessories();
        
        return view('admin.inventory.index', compact(
            'inventories',
            'lowStockProducts',
            'outOfStockProducts',
            'lowStockAccessories',
            'outOfStockAccessories',
            'products',
            'accessories'
        ));
    }

    public function adjustment(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:App\Models\Product,App\Models\Accessory',
            'item_id' => 'required|integer',
            'quantity' => 'required|numeric|not_in:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['price'] = $this->getItemPrice($data['item_id'], $data['item_type']);
        
        $this->inventoryService->stockAdjustment($data);
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Stock adjustment completed successfully.');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'source_type' => 'required|in:App\Models\Product,App\Models\Accessory',
            'source_id' => 'required|integer',
            'destination_type' => 'required|in:App\Models\Product,App\Models\Accessory',
            'destination_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        
        $sourceItem = $this->getItem($data['source_id'], $data['source_type']);
        $destinationItem = $this->getItem($data['destination_id'], $data['destination_type']);
        
        $data['source_name'] = $sourceItem->name ?? 'Unknown';
        $data['destination_name'] = $destinationItem->name ?? 'Unknown';
        $data['price'] = $sourceItem->purchase_price ?? 0;
        
        $this->inventoryService->stockTransfer($data);
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Stock transfer completed successfully.');
    }

    public function damage(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:App\Models\Product,App\Models\Accessory',
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['quantity'] = -abs($data['quantity']);
        $data['price'] = $this->getItemPrice($data['item_id'], $data['item_type']);
        $data['remarks'] = 'Damaged: ' . ($data['remarks'] ?? '');
        
        $this->inventoryService->stockAdjustment($data);
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Damaged stock recorded successfully.');
    }

    public function lost(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:App\Models\Product,App\Models\Accessory',
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['quantity'] = -abs($data['quantity']);
        $data['price'] = $this->getItemPrice($data['item_id'], $data['item_type']);
        $data['remarks'] = 'Lost: ' . ($data['remarks'] ?? '');
        
        $this->inventoryService->stockAdjustment($data);
        
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Lost stock recorded successfully.');
    }

    public function getItems(Request $request)
    {
        $type = $request->get('type');
        $items = [];
        
        if ($type === 'App\Models\Product') {
            $products = $this->productService->getAllProducts();
            foreach ($products as $item) {
                $items[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'current_stock' => $item->current_stock,
                ];
            }
        } elseif ($type === 'App\Models\Accessory') {
            $accessories = $this->accessoryService->getAllAccessories();
            foreach ($accessories as $item) {
                $items[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'current_stock' => $item->current_stock,
                ];
            }
        }
        
        return response()->json($items);
    }

    protected function getItemPrice($itemId, $itemType)
    {
        if ($itemType === 'App\Models\Product') {
            $item = $this->productService->getProductById($itemId);
            return $item ? $item->purchase_price : 0;
        } elseif ($itemType === 'App\Models\Accessory') {
            $item = $this->accessoryService->getAccessoryById($itemId);
            return $item ? $item->purchase_price : 0;
        }
        return 0;
    }

    protected function getItem($itemId, $itemType)
    {
        if ($itemType === 'App\Models\Product') {
            return $this->productService->getProductById($itemId);
        } elseif ($itemType === 'App\Models\Accessory') {
            return $this->accessoryService->getAccessoryById($itemId);
        }
        return null;
    }
}