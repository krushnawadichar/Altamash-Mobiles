<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount(['sales', 'repairJobs'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('has_due') && $request->has_due == '1') {
            $query->where('current_balance', '>', 0);
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:customers,mobile',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:30',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $opening = floatval($request->opening_balance ?? 0);
        $customer = Customer::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'address' => $request->address,
            'gst_number' => $request->gst_number,
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('CUSTOMER_CREATED', 'customers', $customer->id, "Created customer: {$customer->name} ({$customer->mobile})");

        if ($request->ajax()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        return back()->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'sales' => fn($q) => $q->latest()->take(20),
            'payments' => fn($q) => $q->latest()->take(20),
            'repairJobs' => fn($q) => $q->latest()->take(20),
        ]);

        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:customers,mobile,' . $customer->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:30',
            'notes' => 'nullable|string',
        ]);

        $customer->update($request->only(['name', 'mobile', 'email', 'address', 'gst_number', 'notes']));
        ActivityLog::log('CUSTOMER_UPDATED', 'customers', $customer->id, "Updated customer: {$customer->name}");

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists() || $customer->repairJobs()->exists()) {
            return back()->with('error', 'Cannot delete customer with existing sales or repair records.');
        }

        $name = $customer->name;
        $customer->delete();
        ActivityLog::log('CUSTOMER_DELETED', 'customers', null, "Deleted customer: {$name}");

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function collectPayment(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $customer->current_balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_ref' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        SalePayment::create([
            'sale_id' => null,
            'customer_id' => $customer->id,
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_ref' => $request->transaction_ref,
            'notes' => $request->notes ?: 'General due balance settlement',
            'created_by' => Auth::id(),
        ]);

        $customer->current_balance = max(0.00, $customer->current_balance - $request->amount);
        $customer->save();

        ActivityLog::log('CUSTOMER_PAYMENT', 'customers', $customer->id, "Collected due payment of ₹" . number_format($request->amount, 2) . " from {$customer->name}");

        return back()->with('success', 'Payment recorded successfully.');
    }
}
