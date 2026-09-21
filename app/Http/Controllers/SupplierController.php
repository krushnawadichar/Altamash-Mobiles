<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::withCount('purchases')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%");
            });
        }

        if ($request->filled('has_due') && $request->has_due == '1') {
            $query->where('current_balance', '>', 0);
        }

        $suppliers = $query->paginate(15)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:30',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $opening = floatval($request->opening_balance ?? 0);
        $supplier = Supplier::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'gst_number' => $request->gst_number,
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('SUPPLIER_CREATED', 'suppliers', $supplier->id, "Created supplier: {$supplier->name}");

        return back()->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load([
            'purchases' => fn($q) => $q->latest()->take(20),
            'payments' => fn($q) => $q->latest()->take(20),
        ]);

        return view('admin.suppliers.show', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($request->only(['name', 'company_name', 'mobile', 'email', 'address', 'gst_number', 'notes']));
        ActivityLog::log('SUPPLIER_UPDATED', 'suppliers', $supplier->id, "Updated supplier: {$supplier->name}");

        return back()->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'Cannot delete supplier with existing purchase records.');
        }

        $name = $supplier->name;
        $supplier->delete();
        ActivityLog::log('SUPPLIER_DELETED', 'suppliers', null, "Deleted supplier: {$name}");

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    public function makePayment(Request $request, Supplier $supplier)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $supplier->current_balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        PurchasePayment::create([
            'purchase_id' => null,
            'supplier_id' => $supplier->id,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_no' => $request->reference_no,
            'notes' => $request->notes ?: 'General supplier due settlement',
            'created_by' => Auth::id(),
        ]);

        $supplier->current_balance = max(0.00, $supplier->current_balance - $request->amount);
        $supplier->save();

        ActivityLog::log('SUPPLIER_PAYMENT', 'suppliers', $supplier->id, "Paid ₹" . number_format($request->amount, 2) . " to supplier {$supplier->name}");

        return back()->with('success', 'Payment recorded successfully.');
    }
}
