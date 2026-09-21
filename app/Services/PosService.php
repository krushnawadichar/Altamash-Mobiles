<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PosService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Generate unique sequential invoice number
     */
    public function generateInvoiceNo(): string
    {
        $prefix = Setting::get('invoice_prefix', 'INV');
        $year = date('Y');
        $lastSale = Sale::where('invoice_no', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastSale) {
            $parts = explode('-', $lastSale->invoice_no);
            if (count($parts) === 3) {
                $nextNumber = intval($parts[2]) + 1;
            }
        }

        return sprintf("%s-%s-%06d", $prefix, $year, $nextNumber);
    }

    /**
     * Process complete POS checkout in a single database transaction
     */
    public function processSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $customerId = !empty($data['customer_id']) ? $data['customer_id'] : null;
            $customerName = $data['customer_name'] ?? null;
            $customerMobile = $data['customer_mobile'] ?? null;

            if ($customerId) {
                $customer = Customer::lockForUpdate()->find($customerId);
                if ($customer) {
                    $customerName = $customer->name;
                    $customerMobile = $customer->mobile;
                }
            } elseif ($customerMobile) {
                // Find or auto-create customer by mobile
                $customer = Customer::firstOrCreate(
                    ['mobile' => $customerMobile],
                    ['name' => $customerName ?: 'Customer ' . substr($customerMobile, -4)]
                );
                $customerId = $customer->id;
                $customerName = $customer->name;
            }

            $invoiceNo = $this->generateInvoiceNo();
            $subtotal = floatval($data['subtotal'] ?? 0);
            $discountType = $data['discount_type'] ?? 'fixed';
            $discountAmount = floatval($data['discount_amount'] ?? 0);
            $taxPercent = floatval($data['tax_percent'] ?? 0);
            $taxAmount = floatval($data['tax_amount'] ?? 0);
            $grandTotal = floatval($data['grand_total'] ?? ($subtotal - $discountAmount + $taxAmount));
            $paidAmount = floatval($data['paid_amount'] ?? $grandTotal);
            $dueAmount = max(0.00, $grandTotal - $paidAmount);
            $changeAmount = max(0.00, $paidAmount - $grandTotal);
            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'due');
            $paymentMethod = $data['payment_method'] ?? 'cash';

            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'customer_mobile' => $customerMobile,
                'sale_date' => $data['sale_date'] ?? now()->toDateString(),
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_amount' => $discountAmount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'paid_amount' => min($paidAmount, $grandTotal),
                'due_amount' => $dueAmount,
                'change_amount' => $changeAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'status' => 'completed',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Process Items
            foreach ($data['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $qty = intval($itemData['quantity'] ?? 1);
                $unitPrice = floatval($itemData['unit_price'] ?? $product->selling_price);
                $serialId = !empty($itemData['product_serial_id']) ? $itemData['product_serial_id'] : null;
                $serial = null;
                $unitCost = $product->purchase_price;

                if ($product->isMobile()) {
                    if (!$serialId && !empty($itemData['imei'])) {
                        $serial = ProductSerial::where('imei_1', $itemData['imei'])->first();
                        $serialId = $serial?->id;
                    } elseif ($serialId) {
                        $serial = ProductSerial::find($serialId);
                    }

                    if (!$serial) {
                        throw new Exception("IMEI is required for mobile: {$product->name}");
                    }

                    if ($serial->status !== 'available') {
                        throw new Exception("IMEI: {$serial->imei_1} is {$serial->status} and cannot be sold.");
                    }

                    $unitCost = $serial->purchase_price ?: $product->purchase_price;
                } else {
                    // Check stock for accessories
                    if ($product->current_stock < $qty) {
                        throw new Exception("Insufficient stock for {$product->name}. In stock: {$product->current_stock}, Requested: {$qty}");
                    }
                }

                $itemDiscount = floatval($itemData['discount_amount'] ?? 0);
                $itemTax = floatval($itemData['tax_amount'] ?? 0);
                $itemSubtotal = floatval($itemData['subtotal'] ?? (($qty * $unitPrice) - $itemDiscount + $itemTax));

                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_serial_id' => $serialId,
                    'imei' => $serial?->imei_1 ?? ($itemData['imei'] ?? null),
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'unit_cost' => $unitCost,
                    'discount_amount' => $itemDiscount,
                    'tax_amount' => $itemTax,
                    'subtotal' => $itemSubtotal,
                    'warranty_months' => $serial?->warranty_months ?? intval($itemData['warranty_months'] ?? 0),
                ]);

                // Deduct stock and record inventory transaction
                $this->inventoryService->recordMovement(
                    product: $product,
                    transactionType: 'SALE',
                    quantityChange: -$qty,
                    referenceId: $sale->id,
                    referenceType: 'Sale',
                    serial: $serial,
                    unitCost: $unitCost,
                    notes: "POS Sale: {$sale->invoice_no}"
                );

                // If mobile, mark IMEI as sold
                if ($serial) {
                    $this->inventoryService->markImeiSold($serial, $saleItem->id, $unitPrice);
                }
            }

            // Record Payments (Supports Mixed / Split Payment)
            if (!empty($data['payments']) && is_array($data['payments'])) {
                foreach ($data['payments'] as $payment) {
                    $amount = floatval($payment['amount'] ?? 0);
                    if ($amount > 0) {
                        SalePayment::create([
                            'sale_id' => $sale->id,
                            'customer_id' => $customerId,
                            'payment_date' => $data['sale_date'] ?? now()->toDateString(),
                            'amount' => $amount,
                            'payment_method' => $payment['method'] ?? 'cash',
                            'transaction_ref' => $payment['transaction_ref'] ?? null,
                            'notes' => $payment['notes'] ?? null,
                            'created_by' => Auth::id(),
                        ]);
                    }
                }
            } else {
                $actualPaid = min($paidAmount, $grandTotal);
                if ($actualPaid > 0) {
                    SalePayment::create([
                        'sale_id' => $sale->id,
                        'customer_id' => $customerId,
                        'payment_date' => $data['sale_date'] ?? now()->toDateString(),
                        'amount' => $actualPaid,
                        'payment_method' => $paymentMethod,
                        'transaction_ref' => $data['transaction_ref'] ?? null,
                        'notes' => 'Initial POS Payment',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            // Update customer balance if due amount remains
            if ($customerId && $dueAmount > 0) {
                $customer = Customer::find($customerId);
                if ($customer) {
                    $customer->current_balance += $dueAmount;
                    $customer->save();
                }
            }

            ActivityLog::log(
                action: 'SALE_COMPLETED',
                module: 'sales',
                recordId: $sale->id,
                description: "POS Sale completed: {$sale->invoice_no}, Total: ₹" . number_format($sale->grand_total, 2)
            );

            return $sale->load(['items.product', 'items.serial', 'payments', 'customer']);
        });
    }
}
