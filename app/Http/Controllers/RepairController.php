<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repair;
use App\Models\RepairItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{
    public function index()
    {
        $repairs = Repair::with(['items', 'handler'])->latest()->get();

        $totalRepairs = $repairs->count();
        $totalBilled = $repairs->sum(function ($r) {
            return $r->final_cost > 0 ? $r->final_cost : $r->estimated_cost;
        });
        $totalMaterialCost = $repairs->sum('material_cost');
        $totalProfit = $repairs->sum('profit');
        $totalPaid = $repairs->sum('paid_amount');
        $totalDue = $repairs->sum('due_amount');

        return view('admin.repairs.index', compact(
            'repairs',
            'totalRepairs',
            'totalBilled',
            'totalMaterialCost',
            'totalProfit',
            'totalPaid',
            'totalDue'
        ));
    }

    public function create()
    {
        $products = Product::select('id', 'name', 'purchase_price', 'selling_price', 'stock_quantity')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.repairs.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'device_name' => 'required|string|max:255',
            'problem_description' => 'required|string',
            'received_date' => 'required|date',
            'expected_delivery' => 'nullable|date',
            'estimated_cost' => 'nullable|numeric|min:0',
            'material_cost' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'status' => 'required|string',
            'technician_notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $data) {
            // Process material items
            $parsedMaterials = $this->extractMaterials($request);
            $totalMaterialCost = 0;
            foreach ($parsedMaterials as $mat) {
                $totalMaterialCost += (float)$mat['total_cost'];
            }

            // Allow direct manual material cost if spare parts rows were not filled or direct override
            if ($totalMaterialCost == 0 && isset($data['material_cost']) && (float)$data['material_cost'] > 0) {
                $totalMaterialCost = (float)$data['material_cost'];
            }

            $serviceCharge = (float)($data['service_charge'] ?? 0);
            $estimatedCost = (float)($data['estimated_cost'] ?? 0);
            $finalCostInput = isset($data['final_cost']) ? (float)$data['final_cost'] : null;

            if ($finalCostInput !== null && $finalCostInput > 0) {
                $finalCost = $finalCostInput;
            } elseif ($serviceCharge > 0 || $totalMaterialCost > 0) {
                $finalCost = $totalMaterialCost + $serviceCharge;
            } else {
                $finalCost = $estimatedCost;
            }

            $billTotal = $finalCost > 0 ? $finalCost : $estimatedCost;
            $paidAmount = (float)($data['paid_amount'] ?? $data['advance_payment'] ?? 0);
            $dueAmount = max(0, $billTotal - $paidAmount);
            $profit = max(0, $billTotal - $totalMaterialCost);

            $paymentStatus = 'unpaid';
            if ($dueAmount <= 0 && $paidAmount > 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0 && $dueAmount > 0) {
                $paymentStatus = 'partial';
            }

            $data['repair_code'] = 'REP-' . strtoupper(Str::random(5)) . '-' . rand(100, 999);
            $data['handled_by'] = auth()->id();
            $data['material_cost'] = $totalMaterialCost;
            $data['service_charge'] = $serviceCharge;
            $data['final_cost'] = $finalCost;
            $data['advance_payment'] = $paidAmount;
            $data['paid_amount'] = $paidAmount;
            $data['due_amount'] = $dueAmount;
            $data['profit'] = $profit;
            $data['payment_status'] = $paymentStatus;

            // Log initial advance payment if provided
            if ($paidAmount > 0) {
                $stamp = now()->format('d M Y, h:i A');
                $user = auth()->user()?->name ?? 'Admin';
                $method = $data['payment_method'] ?? 'Cash';
                $log = "[{$stamp}] Initial advance payment of ₹" . number_format($paidAmount, 2) . " received via {$method} by {$user}";
                $data['technician_notes'] = !empty($data['technician_notes'])
                    ? $data['technician_notes'] . "\n" . $log
                    : $log;
            }

            $repair = Repair::create($data);

            // Save repair materials
            foreach ($parsedMaterials as $mat) {
                $repair->items()->create($mat);

                // If product from inventory was linked, decrement stock
                if (!empty($mat['product_id'])) {
                    $prod = Product::find($mat['product_id']);
                    if ($prod && $prod->stock_quantity >= $mat['quantity']) {
                        $prod->decrement('stock_quantity', $mat['quantity']);
                    }
                }
            }

            return redirect()->route('admin.repairs.show', $repair)->with('success', 'Repair record created with materials and profit calculation successfully.');
        });
    }

    public function show(Repair $repair)
    {
        $repair->load(['items.product', 'handler']);
        return view('admin.repairs.show', compact('repair'));
    }

    public function edit(Repair $repair)
    {
        $repair->load('items.product');
        $products = Product::select('id', 'name', 'purchase_price', 'selling_price', 'stock_quantity')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.repairs.edit', compact('repair', 'products'));
    }

    public function update(Request $request, Repair $repair)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'device_name' => 'required|string|max:255',
            'problem_description' => 'required|string',
            'received_date' => 'required|date',
            'expected_delivery' => 'nullable|date',
            'estimated_cost' => 'nullable|numeric|min:0',
            'material_cost' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'final_cost' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'status' => 'required|string',
            'technician_notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $repair, $data) {
            $parsedMaterials = $this->extractMaterials($request);
            $totalMaterialCost = 0;
            foreach ($parsedMaterials as $mat) {
                $totalMaterialCost += (float)$mat['total_cost'];
            }

            // Allow direct manual material cost if spare parts rows were not filled or direct override
            if ($totalMaterialCost == 0 && isset($data['material_cost']) && (float)$data['material_cost'] > 0) {
                $totalMaterialCost = (float)$data['material_cost'];
            }

            $serviceCharge = (float)($data['service_charge'] ?? $repair->service_charge);
            $estimatedCost = (float)($data['estimated_cost'] ?? $repair->estimated_cost);
            $finalCostInput = isset($data['final_cost']) ? (float)$data['final_cost'] : null;

            if ($finalCostInput !== null && $finalCostInput > 0) {
                $finalCost = $finalCostInput;
            } elseif ($serviceCharge > 0 || $totalMaterialCost > 0) {
                $finalCost = $totalMaterialCost + $serviceCharge;
            } else {
                $finalCost = $estimatedCost;
            }

            $billTotal = $finalCost > 0 ? $finalCost : $estimatedCost;
            $paidAmount = isset($data['paid_amount']) ? (float)$data['paid_amount'] : (float)($data['advance_payment'] ?? $repair->paid_amount);
            $dueAmount = max(0, $billTotal - $paidAmount);
            $profit = max(0, $billTotal - $totalMaterialCost);

            $paymentStatus = 'unpaid';
            if ($dueAmount <= 0 && $paidAmount > 0) {
                $paymentStatus = 'paid';
            } elseif ($paidAmount > 0 && $dueAmount > 0) {
                $paymentStatus = 'partial';
            }

            $data['material_cost'] = $totalMaterialCost;
            $data['service_charge'] = $serviceCharge;
            $data['final_cost'] = $finalCost;
            $data['paid_amount'] = $paidAmount;
            $data['due_amount'] = $dueAmount;
            $data['profit'] = $profit;
            $data['payment_status'] = $paymentStatus;

            $repair->update($data);

            // Re-sync repair material items
            $repair->items()->delete();
            foreach ($parsedMaterials as $mat) {
                $repair->items()->create($mat);
            }

            return redirect()->route('admin.repairs.show', $repair)->with('success', 'Repair record updated with revised material cost and profit calculation.');
        });
    }

    public function recordPayment(Request $request, Repair $repair)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $paidNow = (float)$request->amount;
        if ($paidNow > (float)$repair->due_amount) {
            return back()->withErrors([
                'amount' => 'Paid amount cannot exceed the customer pending due amount (₹' . number_format($repair->due_amount, 2) . ')'
            ])->with('error', 'Paid amount cannot exceed pending due balance (₹' . number_format($repair->due_amount, 2) . ')')->withInput();
        }

        $newPaid = (float)$repair->paid_amount + $paidNow;
        $billTotal = $repair->final_cost > 0 ? (float)$repair->final_cost : (float)$repair->estimated_cost;
        $newDue = max(0, $billTotal - $newPaid);
        $newStatus = $newDue <= 0 ? 'paid' : 'partial';

        $refValue = $request->payment_reference ?: $request->reference_number;
        $noteValue = $request->payment_notes ?: $request->notes;

        $stamp = now()->format('d M Y, h:i A');
        $user = auth()->user()?->name ?? 'Admin';
        $refText = !empty($refValue) ? " [Ref/Txn: {$refValue}]" : '';
        $noteText = !empty($noteValue) ? " ({$noteValue})" : '';
        $paymentLog = "[{$stamp}] Customer paid ₹" . number_format($paidNow, 2) . " via {$request->payment_method}{$refText}{$noteText} by {$user}";

        $existingNotes = $repair->technician_notes ? $repair->technician_notes . "\n" . $paymentLog : $paymentLog;

        $updateData = [
            'paid_amount' => $newPaid,
            'due_amount' => $newDue,
            'payment_status' => $newStatus,
            'technician_notes' => $existingNotes,
        ];

        if ($newDue <= 0 && $repair->status == 'Ready') {
            $updateData['status'] = 'Delivered';
        }

        $repair->update($updateData);

        return back()->with('success', 'Customer payment of ₹' . number_format($paidNow, 2) . ' received and recorded successfully! Due balance updated.');
    }

    public function print(Repair $repair)
    {
        $repair->load(['items.product', 'handler']);
        return view('admin.repairs.print', compact('repair'));
    }

    public function destroy(Repair $repair)
    {
        $repair->items()->delete();
        $repair->delete();
        return redirect()->route('admin.repairs.index')->with('success', 'Repair record deleted successfully.');
    }

    /**
     * Helper to extract and normalize material items from form input.
     */
    protected function extractMaterials(Request $request): array
    {
        $materials = [];

        // Support array of objects: materials[i][...]
        if ($request->has('materials') && is_array($request->materials)) {
            foreach ($request->materials as $mat) {
                $name = trim($mat['item_name'] ?? $mat['name'] ?? '');
                if (empty($name)) {
                    continue;
                }
                $qty = max(1, (int)($mat['quantity'] ?? 1));
                $unitCost = (float)($mat['unit_cost'] ?? $mat['cost'] ?? 0);
                $totalCost = $qty * $unitCost;

                $materials[] = [
                    'product_id' => !empty($mat['product_id']) ? $mat['product_id'] : null,
                    'item_name' => $name,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                ];
            }
        }
        // Support parallel arrays: material_names[], material_quantities[], material_costs[], material_product_ids[]
        elseif ($request->has('material_names') && is_array($request->material_names)) {
            $names = $request->material_names;
            $quantities = $request->material_quantities ?? [];
            $costs = $request->material_costs ?? [];
            $productIds = $request->material_product_ids ?? [];

            foreach ($names as $idx => $name) {
                $cleanName = trim($name);
                if (empty($cleanName)) {
                    continue;
                }
                $qty = isset($quantities[$idx]) ? max(1, (int)$quantities[$idx]) : 1;
                $unitCost = isset($costs[$idx]) ? (float)$costs[$idx] : 0;
                $totalCost = $qty * $unitCost;
                $prodId = !empty($productIds[$idx]) ? $productIds[$idx] : null;

                $materials[] = [
                    'product_id' => $prodId,
                    'item_name' => $cleanName,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                ];
            }
        }

        return $materials;
    }
}
