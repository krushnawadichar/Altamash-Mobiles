@extends('layouts.admin')

@section('title', 'Job Card - ' . $repair->repair_no)
@section('page_title', 'Print Repair Job Card')

@section('content')
<style>
    .jobcard-print-box {
        background: #fff;
        max-width: 800px;
        margin: 0 auto;
        padding: 30px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    @media print {
        .no-print, #sidebar, #topbar {
            display: none !important;
        }
        body, #main-content, .content-body {
            background: #fff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .jobcard-print-box {
            box-shadow: none !important;
            border: 1px solid #999 !important;
            padding: 15px !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
</style>

<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3 no-print">
        <div>
            <h4 class="fw-bold mb-0">Repair Job Card: {{ $repair->repair_no }}</h4>
            <small class="text-muted">Customer Intake Receipt</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Print Job Card
            </button>
            <a href="{{ route('admin.repairs.jobcard.pdf', $repair) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
            </a>
            <a href="{{ route('admin.repairs.show', $repair) }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="jobcard-print-box">
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-3 mb-3">
            <div class="col-7">
                <h4 class="fw-bold text-primary mb-1">{{ $settings['shop_name'] }}</h4>
                <div class="text-muted small">{{ $settings['shop_address'] }}</div>
                <div class="text-muted small">Phone: {{ $settings['shop_phone'] }} | Email: {{ $settings['shop_email'] }}</div>
            </div>
            <div class="col-5 text-end">
                <span class="badge bg-dark px-3 py-1 mb-1">REPAIR JOB CARD</span>
                <h5 class="fw-bold font-monospace mb-0">{{ $repair->repair_no }}</h5>
                <div class="text-muted small">Date: {{ $repair->received_date->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <!-- Customer & Device Info -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="p-2 border rounded bg-light">
                    <strong class="text-uppercase small text-muted d-block mb-1">Customer Details:</strong>
                    <div class="fw-bold fs-6">{{ $repair->customer_name }}</div>
                    <div>Phone: <strong>+91 {{ $repair->customer_mobile }}</strong></div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-2 border rounded bg-light">
                    <strong class="text-uppercase small text-muted d-block mb-1">Device Details:</strong>
                    <div class="fw-bold fs-6">{{ $repair->model_name }} ({{ $repair->color ?: 'Standard' }})</div>
                    <div>IMEI / Serial: <code>{{ $repair->imei ?: $repair->serial_no ?: 'N/A' }}</code></div>
                </div>
            </div>
        </div>

        <!-- Problem & Intake Info -->
        <div class="border rounded p-3 mb-3">
            <div class="mb-2">
                <strong class="text-danger small text-uppercase d-block">Reported Problem / Defect:</strong>
                <div class="fw-semibold text-dark">{{ $repair->problem_complaint }}</div>
            </div>
            <div class="row pt-2 border-top">
                <div class="col-6">
                    <small class="text-muted d-block">Physical Condition on Intake:</small>
                    <span>{{ $repair->physical_condition ?: 'Normal scratches/wear' }}</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Accessories Received:</small>
                    <span>{{ $repair->accessories_received ?: 'Device only' }}</span>
                </div>
            </div>
        </div>

        <!-- Estimates & Advance -->
        <div class="row g-3 mb-3">
            <div class="col-4">
                <div class="border rounded p-2 text-center bg-light">
                    <small class="text-muted d-block">Estimated Cost</small>
                    <span class="fw-bold fs-6">₹{{ number_format($repair->estimated_cost, 2) }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2 text-center bg-light">
                    <small class="text-muted d-block">Advance Paid</small>
                    <span class="fw-bold fs-6 text-success">₹{{ number_format($repair->advance_amount, 2) }}</span>
                </div>
            </div>
            <div class="col-4">
                <div class="border rounded p-2 text-center bg-light">
                    <small class="text-muted d-block">Est. Delivery Date</small>
                    <span class="fw-bold fs-6">{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d M Y') : 'TBD' }}</span>
                </div>
            </div>
        </div>

        <!-- Terms & Signature -->
        <div class="border-top pt-3 mt-3 text-muted" style="font-size: 0.76rem; line-height: 1.4;">
            <div class="fw-bold text-dark mb-1">Service Terms & Conditions:</div>
            <ol class="ps-3 mb-3">
                <li>Please present this original Job Card at the time of collecting your device.</li>
                <li>Devices not claimed within 30 days after notification are subject to storage charges or disposal.</li>
                <li>The shop is not responsible for data loss during repair. Customers must back up their personal data.</li>
                <li>Repair warranty covers only the repaired issue/replaced parts for {{ $repair->warranty_days }} days.</li>
            </ol>
            <div class="d-flex justify-content-between pt-4">
                <div class="text-center" style="border-top: 1px solid #999; min-width: 150px;">
                    Customer Signature
                </div>
                <div class="text-center" style="border-top: 1px solid #999; min-width: 150px;">
                    Authorized Store Signatory
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
