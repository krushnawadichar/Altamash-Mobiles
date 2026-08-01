<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Inventory;
use App\Repositories\SaleRepository;
use App\Services\ProductService;
use App\Services\AccessoryService;
use App\Services\CustomerService;
use Illuminate\Support\Facades\DB;

class SaleService
{
    protected $saleRepository;
    protected $productService;
    protected $accessoryService;
    protected $customerService;

    public function __construct(
        SaleRepository $saleRepository,
        ProductService $productService,
        AccessoryService $accessoryService,
        CustomerService $customerService
    ) {
        $this->saleRepository = $saleRepository;
        $this->productService = $productService;
        $this->accessoryService = $accessoryService;
        $this->customerService = $customerService;
    }

    public function getAllSales()
    {
        return $this->saleRepository->all();
    }

    public function getSaleById($id)
    {
        return $this->saleRepository->find($id);
    }

    public function createSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals
            $subtotal = 0;
            $items = $data['items'];

            foreach ($items as $item) {
                $subtotal += $item['selling_price'] * $item['quantity'];
            }

            $discount = $data['discount'] ?? 0;
            $gstAmount = ($subtotal - $discount) * (18 / 100);
            $totalAmount = $subtotal - $discount + $gstAmount;
            $paidAmount = $data['paid_amount'] ?? 0;
            $dueAmount = $totalAmount - $paidAmount;

            // Handle customer
            $customerId = $this->handleCustomer($data);

            $saleData = [
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $customerId,
                'sale_date' => $data['sale_date'],
                'payment_method' => $data['payment_method'],
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

            $sale = $this->saleRepository->create($saleData);

            // Create sale details and update inventory
            foreach ($items as $item) {
                $detail = $this->createSaleDetail($sale->id, $item);
                
                // Update inventory (decrease stock)
                $this->updateInventoryForSale($item, $sale->id);
            }

            // Update customer balance
            if ($customerId) {
                $this->customerService->updateBalance($customerId, $dueAmount, 'add');
                $this->customerService->incrementPurchaseCount($customerId);
            }

            return $sale;
        });
    }

    protected function handleCustomer($data)
    {
        if (isset($data['customer_id']) && $data['customer_id']) {
            return $data['customer_id'];
        }

        if (isset($data['customer_name']) && $data['customer_name']) {
            $customer = $this->customerService->createCustomer([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? 'guest_' . time() . '@example.com',
                'phone' => $data['customer_mobile'] ?? 'N/A',
                'address' => $data['customer_address'] ?? null,
                'is_active' => true,
            ]);
            return $customer->id;
        }

        return null;
    }

    protected function createSaleDetail($saleId, $item)
    {
        $purchasePrice = $this->getPurchasePrice($item);
        $profit = ($item['selling_price'] - $purchasePrice) * $item['quantity'];

        $detailData = [
            'sale_id' => $saleId,
            'sellable_id' => $item['id'],
            'sellable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
            'quantity' => $item['quantity'],
            'selling_price' => $item['selling_price'],
            'purchase_price' => $purchasePrice,
            'total' => $item['selling_price'] * $item['quantity'],
            'profit' => $profit,
            'imei' => $item['imei'] ?? null,
            'serial_number' => $item['serial_number'] ?? null,
        ];

        return SaleDetail::create($detailData);
    }

    protected function getPurchasePrice($item)
    {
        if ($item['type'] === 'product') {
            $product = $this->productService->getProductById($item['id']);
            return $product ? $product->purchase_price : 0;
        } else {
            $accessory = $this->accessoryService->getAccessoryById($item['id']);
            return $accessory ? $accessory->purchase_price : 0;
        }
    }

    protected function updateInventoryForSale($item, $saleId)
    {
        $inventoryData = [
            'inventoriable_id' => $item['id'],
            'inventoriable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
            'type' => 'sale',
            'quantity' => -$item['quantity'],
            'price' => $item['selling_price'],
            'total_price' => -($item['selling_price'] * $item['quantity']),
            'reference_id' => $saleId,
            'reference_type' => 'App\Models\Sale',
            'created_by' => auth()->id(),
        ];

        Inventory::create($inventoryData);

        // Update product/accessory stock (decrease)
        if ($item['type'] === 'product') {
            $this->productService->updateStock($item['id'], $item['quantity'], 'subtract');
        } else {
            $this->accessoryService->updateStock($item['id'], $item['quantity'], 'subtract');
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

    public function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Sale::orderBy('id', 'desc')->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1;
        
        return $prefix . '-' . $year . $month . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function deleteSale($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = $this->saleRepository->find($id);
            
            // Reverse inventory updates
            foreach ($sale->details as $detail) {
                // Remove inventory records
                Inventory::where('reference_id', $sale->id)
                    ->where('reference_type', 'App\Models\Sale')
                    ->where('inventoriable_id', $detail->sellable_id)
                    ->where('inventoriable_type', $detail->sellable_type)
                    ->delete();

                // Reverse stock update (add back)
                if ($detail->sellable_type === 'App\Models\Product') {
                    $this->productService->updateStock($detail->sellable_id, $detail->quantity, 'add');
                } else {
                    $this->accessoryService->updateStock($detail->sellable_id, $detail->quantity, 'add');
                }
            }

            // Reverse customer balance
            if ($sale->customer_id) {
                $this->customerService->updateBalance($sale->customer_id, $sale->due_amount, 'subtract');
            }

            return $this->saleRepository->delete($sale);
        });
    }
}