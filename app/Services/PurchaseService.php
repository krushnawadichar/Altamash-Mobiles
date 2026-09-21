<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\Setting;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function generatePurchaseNo(): string
    {
        $prefix = 'PUR';
        $year = date('Y');
        $last = Purchase::where('purchase_no', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($last) {
            $parts = explode('-', $last->purchase_no);
            if (count($parts) === 3) {
                $nextNumber = intval($parts[2]) + 1;
            }
        }

        return sprintf("%s-%s-%06d", $prefix, $year, $nextNumber);
    }

    public function createPurchase(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $supplier = Supplier::lockForUpdate()->findOrFail($data['supplier_id']);
            $purchaseNo = $this->generatePurchaseNo();

            $subtotal = floatval($data['subtotal'] ?? 0);
            $taxAmount = floatval($data['tax_amount'] ?? 0);
            $discountAmount = floatval($data['discount_amount'] ?? 0);
            $grandTotal = floatval($data['grand_total'] ?? ($subtotal + $taxAmount - $discountAmount));
            $paidAmount = floatval($data['paid_amount'] ?? 0);
            $dueAmount = max(0.00, $grandTotal - $paidAmount);
            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'due');

            $purchase = Purchase::create([
                'purchase_no' => $purchaseNo,
                'supplier_id' => $supplier->id,
                'purchase_date' => $data['purchase_date'] ?? now()->toDateString(),
                'supplier_invoice_no' => $data['supplier_invoice_no'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);
                $qty = intval($itemData['quantity']);
                $price = floatval($itemData['purchase_price']);
                $taxPercent = floatval($itemData['tax_percent'] ?? 0);
                $taxAmt = floatval($itemData['tax_amount'] ?? 0);
                $discountAmt = floatval($itemData['discount_amount'] ?? 0);
                $itemSubtotal = floatval($itemData['subtotal'] ?? (($qty * $price) + $taxAmt - $discountAmt));

                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'purchase_price' => $price,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmt,
                    'discount_amount' => $discountAmt,
                    'subtotal' => $itemSubtotal,
                ]);

                // Update product purchase_price if latest
                $product->purchase_price = $price;
                $product->save();

                // If mobile, create product serial records
                if ($product->isMobile() && !empty($itemData['serials'])) {
                    foreach ($itemData['serials'] as $serialData) {
                        if (empty($serialData['imei_1'])) {
                            continue;
                        }

                        // Check if IMEI already exists
                        if (ProductSerial::where('imei_1', $serialData['imei_1'])->exists()) {
                            throw new Exception("IMEI: {$serialData['imei_1']} already exists in the system.");
                        }

                        $serial = ProductSerial::create([
                            'product_id' => $product->id,
                            'imei_1' => $serialData['imei_1'],
                            'imei_2' => $serialData['imei_2'] ?? null,
                            'serial_no' => $serialData['serial_no'] ?? null,
                            'color' => $serialData['color'] ?? null,
                            'ram' => $serialData['ram'] ?? null,
                            'storage' => $serialData['storage'] ?? null,
                            'warranty_months' => intval($serialData['warranty_months'] ?? 12),
                            'purchase_price' => $price,
                            'purchase_item_id' => $purchaseItem->id,
                            'status' => 'available',
                        ]);

                        // Stock movement per serial
                        $this->inventoryService->recordMovement(
                            product: $product,
                            transactionType: 'PURCHASE',
                            quantityChange: 1,
                            referenceId: $purchase->id,
                            referenceType: 'Purchase',
                            serial: $serial,
                            unitCost: $price,
                            notes: "Purchase {$purchase->purchase_no} - IMEI: {$serial->imei_1}"
                        );
                    }
                } else {
                    // Non-mobile or bulk accessories stock IN
                    $this->inventoryService->recordMovement(
                        product: $product,
                        transactionType: 'PURCHASE',
                        quantityChange: $qty,
                        referenceId: $purchase->id,
                        referenceType: 'Purchase',
                        serial: null,
                        unitCost: $price,
                        notes: "Purchase {$purchase->purchase_no} - Stock IN"
                    );
                }
            }

            // Record initial payment if provided
            if ($paidAmount > 0) {
                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $supplier->id,
                    'payment_date' => $data['purchase_date'] ?? now()->toDateString(),
                    'amount' => $paidAmount,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'reference_no' => $data['payment_reference'] ?? null,
                    'notes' => 'Initial Purchase Payment',
                    'created_by' => Auth::id(),
                ]);
            }

            // Update supplier balance
            if ($dueAmount > 0) {
                $supplier->current_balance += $dueAmount;
                $supplier->save();
            }

            ActivityLog::log(
                action: 'PURCHASE_CREATED',
                module: 'purchases',
                recordId: $purchase->id,
                description: "Purchase created: {$purchase->purchase_no}, Supplier: {$supplier->name}, Total: ₹" . number_format($purchase->grand_total, 2)
            );

            return $purchase;
        });
    }
}
