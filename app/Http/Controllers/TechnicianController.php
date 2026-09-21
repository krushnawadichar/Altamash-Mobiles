<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = Technician::withCount([
            'repairJobs',
            'repairJobs as pending_repairs_count' => function ($q) {
                $q->whereNotIn('status', ['Delivered', 'Cancelled']);
            },
            'repairJobs as completed_repairs_count' => function ($q) {
                $q->where('status', 'Delivered');
            },
        ])->latest()->paginate(15);

        return view('admin.technicians.index', compact('technicians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'specialization' => 'nullable|string',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $tech = Technician::create($request->all());
        ActivityLog::log('TECHNICIAN_CREATED', 'technicians', $tech->id, "Created technician: {$tech->name}");

        return back()->with('success', 'Technician registered successfully.');
    }

    public function show(Technician $technician)
    {
        $technician->load(['repairJobs' => function ($q) {
            $q->latest()->take(30);
        }]);

        return view('admin.technicians.show', compact('technician'));
    }

    public function update(Request $request, Technician $technician)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'specialization' => 'nullable|string',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $technician->update($request->all());
        ActivityLog::log('TECHNICIAN_UPDATED', 'technicians', $technician->id, "Updated technician: {$technician->name}");

        return back()->with('success', 'Technician updated successfully.');
    }

    public function destroy(Technician $technician)
    {
        if ($technician->repairJobs()->exists()) {
            return back()->with('error', 'Cannot delete technician who has assigned repair jobs.');
        }

        $name = $technician->name;
        $technician->delete();
        ActivityLog::log('TECHNICIAN_DELETED', 'technicians', null, "Deleted technician: {$name}");

        return redirect()->route('admin.technicians.index')->with('success', 'Technician deleted successfully.');
    }
}
