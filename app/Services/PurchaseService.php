<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Inventory;
use App\Repositories\PurchaseRepository;
use App\Services\ProductService;
use App\Services\AccessoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    protected $purchaseRepository;
    protected $productService;
    protected $accessoryService;

    public function __construct(
        PurchaseRepository $purchaseRepository,
        ProductService $productService,
        AccessoryService $accessoryService
    ) {
        $this->purchaseRepository = $purchaseRepository;
        $this->productService = $productService;
        $this->accessoryService = $accessoryService;
    }

    public function getAllPurchases()
    {
        return $this->purchaseRepository->all();
    }

    public function getPurchaseById($id)
    {
        return $this->purchaseRepository->find($id);
    }

    public function createPurchase(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals
            $subtotal = 0;
            $items = $data['items'];

            foreach ($items as $item) {
                $subtotal += $item['purchase_price'] * $item['quantity'];
            }

            $discount = $data['discount'] ?? 0;
            $gstAmount = ($subtotal - $discount) * (18 / 100); // Assuming 18% GST
            $totalAmount = $subtotal - $discount + $gstAmount;
            $paidAmount = $data['paid_amount'] ?? 0;
            $dueAmount = $totalAmount - $paidAmount;

            $purchaseData = [
                'invoice_number' => $this->generateInvoiceNumber(),
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'gst_amount' => $gstAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $this->getPaymentStatus($paidAmount, $totalAmount),
                'notes' => $data['notes'] ?? null,
                'status' => 'completed',
                'created_by' => auth()->id(),
            ];

            $purchase = $this->purchaseRepository->create($purchaseData);

            // Create purchase details and update inventory
            foreach ($items as $item) {
                $detail = $this->createPurchaseDetail($purchase->id, $item);
                
                // Update inventory
                $this->updateInventory($item, 'purchase', $purchase->id);
            }

            // Update supplier balance
            $this->updateSupplierBalance($data['supplier_id'], $totalAmount, $paidAmount);

            return $purchase;
        });
    }

    protected function createPurchaseDetail($purchaseId, $item)
    {
        $detailData = [
            'purchase_id' => $purchaseId,
            'purchasable_id' => $item['id'],
            'purchasable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
            'quantity' => $item['quantity'],
            'purchase_price' => $item['purchase_price'],
            'selling_price' => $item['selling_price'],
            'total' => $item['purchase_price'] * $item['quantity'],
        ];

        return PurchaseDetail::create($detailData);
    }

    protected function updateInventory($item, $type, $referenceId)
    {
        $inventoryData = [
            'inventoriable_id' => $item['id'],
            'inventoriable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
            'type' => $type,
            'quantity' => $item['quantity'],
            'price' => $item['purchase_price'],
            'total_price' => $item['purchase_price'] * $item['quantity'],
            'reference_id' => $referenceId,
            'reference_type' => 'App\Models\Purchase',
            'created_by' => auth()->id(),
        ];

        Inventory::create($inventoryData);

        // Update product/accessory stock
        if ($item['type'] === 'product') {
            $this->productService->updateStock($item['id'], $item['quantity'], 'add');
        } else {
            $this->accessoryService->updateStock($item['id'], $item['quantity'], 'add');
        }
    }

    protected function updateSupplierBalance($supplierId, $totalAmount, $paidAmount)
    {
        $supplier = \App\Models\Supplier::find($supplierId);
        if ($supplier) {
            $supplier->current_balance += $totalAmount - $paidAmount;
            $supplier->save();
        }
    }

    protected function getPaymentStatus($paidAmount, $totalAmount)
    {
        if ($paidAmount >= $totalAmount) {
            return 'paid';
        } elseif ($paidAmount > 0) {
            return 'partial';
        }
        return 'pending';
    }

    protected function generateInvoiceNumber()
    {
        $prefix = 'PO';
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Purchase::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return $prefix . '-' . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function updatePurchase($id, array $data)
    {
        // Implementation for updating purchase
    }

    public function deletePurchase($id)
    {
        return DB::transaction(function () use ($id) {
            $purchase = $this->purchaseRepository->find($id);
            
            // Reverse inventory updates
            foreach ($purchase->details as $detail) {
                // Remove inventory records
                Inventory::where('reference_id', $purchase->id)
                    ->where('reference_type', 'App\Models\Purchase')
                    ->where('inventoriable_id', $detail->purchasable_id)
                    ->where('inventoriable_type', $detail->purchasable_type)
                    ->delete();

                // Reverse stock update
                if ($detail->purchasable_type === 'App\Models\Product') {
                    $this->productService->updateStock($detail->purchasable_id, $detail->quantity, 'subtract');
                } else {
                    $this->accessoryService->updateStock($detail->purchasable_id, $detail->quantity, 'subtract');
                }
            }

            // Reverse supplier balance
            $supplier = \App\Models\Supplier::find($purchase->supplier_id);
            if ($supplier) {
                $supplier->current_balance -= ($purchase->total_amount - $purchase->paid_amount);
                $supplier->save();
            }

            return $this->purchaseRepository->delete($purchase);
        });
    }
}