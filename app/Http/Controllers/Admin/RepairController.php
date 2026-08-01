<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RepairRequest;
use App\Services\RepairService;
use App\Services\CustomerService;
use App\Services\RepairStatusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    protected $repairService;
    protected $customerService;
    protected $repairStatusService;

    public function __construct(
        RepairService $repairService,
        CustomerService $customerService,
        RepairStatusService $repairStatusService
    ) {
        $this->repairService = $repairService;
        $this->customerService = $customerService;
        $this->repairStatusService = $repairStatusService;
    }

    public function index()
    {
        $repairs = $this->repairService->getAllRepairs();
        return view('admin.repairs.index', compact('repairs'));
    }

    public function create()
    {
        $customers = $this->customerService->getActiveCustomers();
        $statuses = $this->repairStatusService->getActiveRepairStatuses();
        return view('admin.repairs.create', compact('customers', 'statuses'));
    }

    public function store(RepairRequest $request)
    {
        $this->repairService->createRepair($request->validated());
        return redirect()->route('admin.repairs.index')
            ->with('success', 'Repair created successfully.');
    }

    public function show($id)
    {
        $repair = $this->repairService->getRepairById($id);
        return view('admin.repairs.show', compact('repair'));
    }

    public function edit($id)
    {
        $repair = $this->repairService->getRepairById($id);
        $customers = $this->customerService->getActiveCustomers();
        $statuses = $this->repairStatusService->getActiveRepairStatuses();
        return view('admin.repairs.edit', compact('repair', 'customers', 'statuses'));
    }

    public function update(RepairRequest $request, $id)
    {
        $this->repairService->updateRepair($id, $request->validated());
        return redirect()->route('admin.repairs.index')
            ->with('success', 'Repair updated successfully.');
    }

    public function destroy($id)
    {
        $this->repairService->deleteRepair($id);
        return redirect()->route('admin.repairs.index')
            ->with('success', 'Repair deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $this->repairService->updateStatus($id, $request->status_id);
        return redirect()->route('admin.repairs.index')
            ->with('success', 'Repair status updated successfully.');
    }

    public function print($id)
    {
        $repair = $this->repairService->getRepairById($id);
        return view('admin.repairs.print', compact('repair'));
    }

    public function pdf($id)
    {
        $repair = $this->repairService->getRepairById($id);
        $pdf = Pdf::loadView('admin.repairs.pdf', compact('repair'));
        return $pdf->download('repair-' . $repair->repair_number . '.pdf');
    }
}