<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Repair;
use Illuminate\Support\Str;

class RepairController extends Controller
{
    public function index()
    {
        $repairs = Repair::latest()->get();
        return view('admin.repairs.index', compact('repairs'));
    }

    public function create()
    {
        return view('admin.repairs.create');
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
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|string',
        ]);

        $data['repair_code'] = 'REP-' . strtoupper(Str::random(5)) . '-' . rand(100,999);
        $data['handled_by'] = auth()->id();

        Repair::create($data);

        return redirect()->route('admin.repairs.index')->with('success', 'Repair job added successfully.');
    }

    public function edit(Repair $repair)
    {
        return view('admin.repairs.edit', compact('repair'));
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
            'final_cost' => 'nullable|numeric|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'technician_notes' => 'nullable|string',
        ]);

        $repair->update($data);

        return redirect()->route('admin.repairs.index')->with('success', 'Repair job updated successfully.');
    }

    public function destroy(Repair $repair)
    {
        $repair->delete();
        return redirect()->route('admin.repairs.index')->with('success', 'Repair job deleted successfully.');
    }
}
