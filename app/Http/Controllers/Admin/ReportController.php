<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $data = $this->reportService->getSalesReport($request->all());
        return view('admin.reports.sales', compact('data'));
    }

    public function purchases(Request $request)
    {
        $data = $this->reportService->getPurchaseReport($request->all());
        return view('admin.reports.purchases', compact('data'));
    }

    public function profit(Request $request)
    {
        $data = $this->reportService->getProfitReport($request->all());
        return view('admin.reports.profit', compact('data'));
    }

    public function expenses(Request $request)
    {
        $data = $this->reportService->getExpenseReport($request->all());
        return view('admin.reports.expenses', compact('data'));
    }

    public function repairs(Request $request)
    {
        $data = $this->reportService->getRepairReport($request->all());
        return view('admin.reports.repairs', compact('data'));
    }

    public function inventory(Request $request)
    {
        $data = $this->reportService->getInventoryReport($request->all());
        return view('admin.reports.inventory', compact('data'));
    }

    public function customers(Request $request)
    {
        $data = $this->reportService->getCustomerReport($request->all());
        return view('admin.reports.customers', compact('data'));
    }

    public function suppliers(Request $request)
    {
        $data = $this->reportService->getSupplierReport($request->all());
        return view('admin.reports.suppliers', compact('data'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type');
        $format = $request->get('format', 'excel');
        $data = $this->reportService->getReportData($type, $request->all());
        
        if ($format === 'excel') {
            return Excel::download(new ReportsExport($data, $type), $type . '-report.xlsx');
        } elseif ($format === 'csv') {
            return Excel::download(new ReportsExport($data, $type), $type . '-report.csv', \Maatwebsite\Excel\Excel::CSV);
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.pdf', compact('data', 'type'));
            return $pdf->download($type . '-report.pdf');
        }
        
        return back()->with('error', 'Invalid export format.');
    }
}