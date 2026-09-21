<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RepairJob;
use App\Models\RepairPartUsed;
use App\Models\RepairPayment;
use App\Models\RepairStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class RepairService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function generateRepairNo(): string
    {
        $prefix = 'REP';
        $year = date('Y');
        $last = RepairJob::where('repair_no', 'like', "{$prefix}-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($last) {
            $parts = explode('-', $last->repair_no);
            if (count($parts) === 3) {
                $nextNumber = intval($parts[2]) + 1;
            }
        }

        return sprintf("%s-%s-%06d", $prefix, $year, $nextNumber);
    }

    public function createRepairJob(array $data): RepairJob
    {
        return DB::transaction(function () use ($data) {
            $repairNo = $this->generateRepairNo();
            $customerMobile = $data['customer_mobile'];
            $customerName = $data['customer_name'];

            $customer = Customer::firstOrCreate(
                ['mobile' => $customerMobile],
                ['name' => $customerName]
            );

            $estimatedCost = floatval($data['estimated_cost'] ?? 0);
            $finalCost = floatval($data['final_cost'] ?? $estimatedCost);
            $advanceAmount = floatval($data['advance_amount'] ?? 0);
            $dueAmount = max(0.00, $finalCost - $advanceAmount);

            $repair = RepairJob::create([
                'repair_no' => $repairNo,
                'customer_id' => $customer->id,
                'customer_name' => $customerName,
                'customer_mobile' => $customerMobile,
                'brand_id' => $data['brand_id'] ?? null,
                'model_name' => $data['model_name'],
                'imei' => $data['imei'] ?? null,
                'serial_no' => $data['serial_no'] ?? null,
                'color' => $data['color'] ?? null,
                'problem_complaint' => $data['problem_complaint'],
                'physical_condition' => $data['physical_condition'] ?? null,
                'accessories_received' => $data['accessories_received'] ?? null,
                'estimated_cost' => $estimatedCost,
                'final_cost' => $finalCost,
                'advance_amount' => $advanceAmount,
                'paid_amount' => $advanceAmount,
                'due_amount' => $dueAmount,
                'technician_id' => $data['technician_id'] ?? null,
                'status' => 'Received',
                'received_date' => now(),
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'warranty_days' => intval($data['warranty_days'] ?? 0),
                'notes' => $data['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            RepairStatusLog::create([
                'repair_job_id' => $repair->id,
                'from_status' => 'New',
                'to_status' => 'Received',
                'notes' => 'Job Card created',
                'created_by' => Auth::id(),
            ]);

            if ($advanceAmount > 0) {
                RepairPayment::create([
                    'repair_job_id' => $repair->id,
                    'payment_date' => now()->toDateString(),
                    'amount' => $advanceAmount,
                    'payment_type' => 'advance',
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'transaction_ref' => $data['transaction_ref'] ?? null,
                    'notes' => 'Advance payment received on intake',
                    'created_by' => Auth::id(),
                ]);
            }

            ActivityLog::log(
                action: 'REPAIR_CREATED',
                module: 'repairs',
                recordId: $repair->id,
                description: "Repair Job Card created: {$repair->repair_no} for {$repair->customer_name} ({$repair->model_name})"
            );

            return $repair;
        });
    }

    public function addPartUsed(RepairJob $repair, int $productId, int $quantity = 1, ?float $unitPrice = null): RepairPartUsed
    {
        return DB::transaction(function () use ($repair, $productId, $quantity, $unitPrice) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            if ($product->current_stock < $quantity) {
                throw new Exception("Insufficient stock for spare part: {$product->name}. In stock: {$product->current_stock}");
            }

            $price = $unitPrice !== null ? $unitPrice : $product->selling_price;
            $subtotal = $price * $quantity;

            $partUsed = RepairPartUsed::create([
                'repair_job_id' => $repair->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => $product->purchase_price,
                'unit_price' => $price,
                'subtotal' => $subtotal,
            ]);

            // Deduct stock immediately
            $this->inventoryService->recordMovement(
                product: $product,
                transactionType: 'REPAIR_PART_USED',
                quantityChange: -$quantity,
                referenceId: $repair->id,
                referenceType: 'RepairJob',
                serial: null,
                unitCost: $product->purchase_price,
                notes: "Used in Repair: {$repair->repair_no}"
            );

            // Recalculate repair final cost
            $totalPartsCost = $repair->partsUsed()->sum('subtotal');
            if ($totalPartsCost > $repair->final_cost) {
                $repair->final_cost = $totalPartsCost;
            }
            $repair->due_amount = max(0.00, $repair->final_cost - $repair->paid_amount);
            $repair->save();

            ActivityLog::log(
                action: 'REPAIR_PART_ADDED',
                module: 'repairs',
                recordId: $repair->id,
                description: "Added spare part {$product->name} (Qty: {$quantity}) to Repair: {$repair->repair_no}"
            );

            return $partUsed;
        });
    }

    public function updateStatus(RepairJob $repair, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $repair->status;
        if ($oldStatus === $newStatus) {
            return;
        }

        $repair->status = $newStatus;

        if ($newStatus === 'Repair Completed') {
            $repair->completed_date = now();
        } elseif ($newStatus === 'Delivered') {
            $repair->delivered_date = now();
        }

        $repair->save();

        RepairStatusLog::create([
            'repair_job_id' => $repair->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $notes,
            'created_by' => Auth::id(),
        ]);

        ActivityLog::log(
            action: 'REPAIR_STATUS_CHANGED',
            module: 'repairs',
            recordId: $repair->id,
            description: "Repair {$repair->repair_no} status changed: {$oldStatus} -> {$newStatus}"
        );
    }

    public function addPayment(RepairJob $repair, float $amount, string $method = 'cash', string $type = 'partial', ?string $ref = null, ?string $notes = null): RepairPayment
    {
        return DB::transaction(function () use ($repair, $amount, $method, $type, $ref, $notes) {
            $payment = RepairPayment::create([
                'repair_job_id' => $repair->id,
                'payment_date' => now()->toDateString(),
                'amount' => $amount,
                'payment_type' => $type,
                'payment_method' => $method,
                'transaction_ref' => $ref,
                'notes' => $notes,
                'created_by' => Auth::id(),
            ]);

            $repair->paid_amount += $amount;
            $repair->due_amount = max(0.00, $repair->final_cost - $repair->paid_amount);
            $repair->save();

            ActivityLog::log(
                action: 'REPAIR_PAYMENT_RECEIVED',
                module: 'repairs',
                recordId: $repair->id,
                description: "Repair {$repair->repair_no} payment received: ₹" . number_format($amount, 2)
            );

            return $payment;
        });
    }
}
