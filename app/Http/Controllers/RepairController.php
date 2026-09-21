<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\Product;
use App\Models\RepairJob;
use App\Models\Setting;
use App\Models\Technician;
use App\Services\RepairService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    protected RepairService $repairService;

    public function __construct(RepairService $repairService)
    {
        $this->repairService = $repairService;
    }

    public function index(Request $request)
    {
        $query = RepairJob::with(['brand', 'technician', 'customer'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('repair_no', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_mobile', 'like', "%{$s}%")
                  ->orWhere('model_name', 'like', "%{$s}%")
                  ->orWhere('imei', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        $repairs = $query->paginate(15)->withQueryString();
        $technicians = Technician::where('status', 'active')->get();

        $statuses = [
            'Received',
            'Diagnosis Pending',
            'Under Diagnosis',
            'Estimate Given',
            'Customer Approval Pending',
            'Approved',
            'Repairing',
            'Waiting for Parts',
            'Repair Completed',
            'Ready for Delivery',
            'Delivered',
            'Cancelled'
        ];

        return view('admin.repairs.index', compact('repairs', 'technicians', 'statuses'));
    }

    public function create()
    {
        $brands = Brand::where('status', 'active')->get();
        $technicians = Technician::where('status', 'active')->get();
        $customers = Customer::latest()->take(30)->get();

        return view('admin.repairs.create', compact('brands', 'technicians', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_mobile' => 'required|string|max:20',
            'model_name' => 'required|string|max:255',
            'problem_complaint' => 'required|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $repair = $this->repairService->createRepairJob($request->all());
            return redirect()->route('admin.repairs.show', $repair)->with('success', "Repair Job {$repair->repair_no} created successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating repair job: ' . $e->getMessage());
        }
    }

    public function show(RepairJob $repair)
    {
        $repair->load([
            'brand',
            'technician',
            'customer',
            'partsUsed.product',
            'payments.creator',
            'statusLogs.creator',
            'creator'
        ]);

        $statuses = [
            'Received',
            'Diagnosis Pending',
            'Under Diagnosis',
            'Estimate Given',
            'Customer Approval Pending',
            'Approved',
            'Repairing',
            'Waiting for Parts',
            'Repair Completed',
            'Ready for Delivery',
            'Delivered',
            'Cancelled'
        ];

        $technicians = Technician::where('status', 'active')->get();
        $spareParts = Product::where('status', 'active')
            ->where(function ($q) {
                $q->where('type', 'spare_part')->orWhere('type', 'accessory');
            })
            ->where('current_stock', '>', 0)
            ->get();

        return view('admin.repairs.show', compact('repair', 'statuses', 'technicians', 'spareParts'));
    }

    public function addPart(Request $request, RepairJob $repair)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->repairService->addPartUsed(
                repair: $repair,
                productId: $request->product_id,
                quantity: $request->quantity,
                unitPrice: $request->filled('unit_price') ? floatval($request->unit_price) : null
            );

            return back()->with('success', 'Part added to repair and deducted from inventory.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add part: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, RepairJob $repair)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'technician_id' => 'nullable|exists:technicians,id',
        ]);

        if ($request->filled('technician_id')) {
            $repair->technician_id = $request->technician_id;
            $repair->save();
        }

        $this->repairService->updateStatus($repair, $request->status, $request->notes);

        return back()->with('success', "Status updated to {$request->status}.");
    }

    public function addPayment(Request $request, RepairJob $repair)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $repair->due_amount,
            'payment_method' => 'required|string',
            'payment_type' => 'required|in:advance,partial,final',
            'transaction_ref' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $this->repairService->addPayment(
            repair: $repair,
            amount: floatval($request->amount),
            method: $request->payment_method,
            type: $request->payment_type,
            ref: $request->transaction_ref,
            notes: $request->notes
        );

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function jobCard(RepairJob $repair)
    {
        $repair->load(['brand', 'technician', 'customer', 'creator']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'terms_conditions' => Setting::get('terms_conditions', ''),
        ];

        return view('admin.repairs.jobcard', compact('repair', 'settings'));
    }

    public function invoice(RepairJob $repair)
    {
        $repair->load(['brand', 'technician', 'customer', 'partsUsed.product', 'payments', 'creator']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'gst_number' => Setting::get('gst_number', ''),
            'terms_conditions' => Setting::get('terms_conditions', ''),
        ];

        return view('admin.repairs.invoice', compact('repair', 'settings'));
    }

    public function pdfJobCard(RepairJob $repair)
    {
        $repair->load(['brand', 'technician', 'customer', 'creator']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'terms_conditions' => Setting::get('terms_conditions', ''),
        ];

        $pdf = Pdf::loadView('admin.repairs.jobcard_pdf', compact('repair', 'settings'));
        return $pdf->download("JobCard-{$repair->repair_no}.pdf");
    }

    public function pdfInvoice(RepairJob $repair)
    {
        $repair->load(['brand', 'technician', 'customer', 'partsUsed.product', 'payments', 'creator']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'MobileCare POS'),
            'shop_address' => Setting::get('shop_address', ''),
            'shop_phone' => Setting::get('shop_phone', ''),
            'gst_number' => Setting::get('gst_number', ''),
            'terms_conditions' => Setting::get('terms_conditions', ''),
        ];

        $pdf = Pdf::loadView('admin.repairs.invoice_pdf', compact('repair', 'settings'));
        return $pdf->download("RepairInvoice-{$repair->repair_no}.pdf");
    }
}
