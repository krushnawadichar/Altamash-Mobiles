@extends('layouts.admin')

@section('title', 'Repair Bill #' . $repair->repair_no)
@section('page_title', 'Repair Delivery Bill')

@section('content')
<style>
    .repair-invoice-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        max-width: 820px;
        margin: 0 auto;
        padding: 40px;
        border: 1px solid #e2e8f0;
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
        .repair-invoice-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
</style>

<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3 no-print">
        <div>
            <h4 class="fw-bold mb-0">Repair Bill: {{ $repair->repair_no }}</h4>
            <small class="text-muted">Device: {{ $repair->model_name }} &bull; Customer: {{ $repair->customer_name }}</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Print Delivery Bill
            </button>
            <a href="{{ route('admin.repairs.invoice.pdf', $repair) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
            </a>
            <a href="{{ route('admin.repairs.show', $repair) }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="repair-invoice-card">
        <!-- Header -->
        <div class="row align-items-center border-bottom pb-4 mb-4">
            <div class="col-sm-7">
                <h3 class="fw-bold text-primary mb-1">{{ $settings['shop_name'] }}</h3>
                <p class="text-muted small mb-2">{{ $settings['shop_tagline'] }}</p>
                <div class="text-muted small">
                    <div><i class="bi bi-geo-alt me-1"></i>{{ $settings['shop_address'] }}</div>
                    <div><i class="bi bi-telephone me-1"></i>{{ $settings['shop_phone'] }} | <i class="bi bi-envelope me-1"></i>{{ $settings['shop_email'] }}</div>
                    @if(!empty($settings['gst_number']))
                        <div><strong>GSTIN:</strong> <code>{{ $settings['gst_number'] }}</code></div>
                    @endif
                </div>
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <span class="badge bg-success text-uppercase px-3 py-2 fs-6 mb-2">REPAIR DELIVERY BILL</span>
                <h5 class="fw-bold mb-1">{{ $repair->repair_no }}</h5>
                <div class="text-muted small">Delivered: <strong>{{ date('d M Y') }}</strong></div>
                <div class="text-muted small">Technician: {{ $repair->technician?->name ?? 'Service Center' }}</div>
            </div>
        </div>

        <!-- Customer & Device Overview -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.05em;">Customer:</h6>
                <div class="fw-bold fs-6">{{ $repair->customer_name }}</div>
                <div class="text-muted small"><i class="bi bi-telephone me-1"></i>+91 {{ $repair->customer_mobile }}</div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.05em;">Serviced Device:</h6>
                <div class="fw-bold fs-6">{{ $repair->model_name }}</div>
                <div class="text-muted small">IMEI / Serial: <code>{{ $repair->imei ?: $repair->serial_no ?: 'N/A' }}</code></div>
                <div class="text-muted small">Warranty: <strong>{{ $repair->warranty_days }} Days on Service</strong></div>
            </div>
        </div>

        <!-- Problem Solved Note -->
        <div class="p-2 bg-light rounded border mb-4 small">
            <strong class="text-muted">Issue Repaired:</strong> {{ $repair->problem_complaint }}
        </div>

        <!-- Parts & Labor Itemized Table -->
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 55%;">Description (Components Replaced & Services)</th>
                        <th class="text-center" style="width: 10%;">Qty</th>
                        <th class="text-end" style="width: 15%;">Unit Price</th>
                        <th class="text-end" style="width: 15%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $idx = 1; @endphp
                    @foreach($repair->partsUsed as $part)
                    <tr>
                        <td>{{ $idx++ }}</td>
                        <td>
                            <div class="fw-bold">{{ $part->product->name }}</div>
                            <small class="text-muted">Replacement Spare Part</small>
                        </td>
                        <td class="text-center">{{ $part->quantity }}</td>
                        <td class="text-end">₹{{ number_format($part->unit_price, 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($part->subtotal, 2) }}</td>
                    </tr>
                    @endforeach

                    @php
                        $partsTotal = $repair->partsUsed->sum('subtotal');
                        $laborCharge = max(0, $repair->final_cost - $partsTotal);
                    @endphp

                    @if($laborCharge > 0 || $repair->partsUsed->isEmpty())
                    <tr>
                        <td>{{ $idx }}</td>
                        <td>
                            <div class="fw-bold">Technical Labor & Diagnostics Service Fee</div>
                            <small class="text-muted">Bench testing, chip-level diagnostics & repair servicing</small>
                        </td>
                        <td class="text-center">1</td>
                        <td class="text-end">₹{{ number_format($laborCharge, 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($laborCharge, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Billing Totals -->
        <div class="row justify-content-end mb-4">
            <div class="col-sm-6 col-md-5">
                <div class="d-flex justify-content-between mb-2 fs-5">
                    <span class="fw-bold">Total Bill:</span>
                    <span class="fw-bold text-primary">₹{{ number_format($repair->final_cost, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Advance / Paid:</span>
                    <span class="fw-bold">₹{{ number_format($repair->paid_amount, 2) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between {{ $repair->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                    <span class="fw-bold">Balance Amount:</span>
                    <span class="fw-bold fs-5">₹{{ number_format($repair->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Signatures & Footer -->
        <div class="border-top pt-4 text-muted small">
            <div class="d-flex justify-content-between pt-5">
                <div class="text-center" style="border-top: 1px solid #999; min-width: 160px;">
                    Customer Acceptance
                </div>
                <div class="text-center" style="border-top: 1px solid #999; min-width: 160px;">
                    Service Manager
                </div>
            </div>
            <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.78rem;">
                {{ $settings['invoice_footer'] }}
            </div>
        </div>
    </div>

</div>
@endsection
