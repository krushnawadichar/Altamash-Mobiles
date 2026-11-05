<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Receipt - {{ $repair->repair_code }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .a4-page {
            width: 210mm;
            min-height: 296mm;
            padding: 12mm 15mm;
            margin: 10mm auto;
            border: 1px #cbd5e1 solid;
            border-radius: 6px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            position: relative;
            box-sizing: border-box;
        }
        .repair-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .repair-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .table > :not(caption) > * > * {
            padding: 8px 12px;
        }
        .table-primary-custom th {
            background-color: #1e293b !important;
            color: #ffffff !important;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 0.95rem;
        }
        .grand-total-row {
            background-color: #eff6ff !important;
            font-weight: bold;
            font-size: 1.15rem;
            color: #1d4ed8;
        }
        .due-row {
            background-color: #fef2f2 !important;
            font-weight: bold;
            color: #dc2626;
        }
        .signature-area {
            margin-top: 35px;
            padding-top: 15px;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .a4-page {
                margin: 0;
                padding: 10mm 15mm;
                border: initial;
                border-radius: initial;
                width: 210mm;
                min-height: 297mm;
                box-shadow: initial;
                background: initial;
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container text-center my-3 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-printer me-2" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg> Print Repair Receipt
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-4 ms-2">Close</button>
    </div>

    <div class="a4-page">
        <!-- Header -->
        <div class="row repair-header align-items-center">
            <div class="col-7">
                <h2 class="fw-bold mb-1 text-dark">ALTAMASH MOBILE</h2>
                <p class="text-muted small mb-0">
                    New narsala road, opposite Dhanashree apartment<br>
                    <strong>Phone:</strong> +91 88884 94747 | <strong>Email:</strong> altamashmobile1@gmail.com
                </p>
            </div>
            <div class="col-5 text-end">
                <div class="repair-title">REPAIR RECEIPT</div>
                <div class="fw-bold text-dark mt-1">Repair Code: {{ $repair->repair_code }}</div>
                <div class="text-muted small">Date Received: {{ $repair->received_date->format('d M, Y') }}</div>
            </div>
        </div>

        <!-- Customer & Device Information -->
        <div class="row mb-3 g-3">
            <div class="col-6">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-uppercase fw-bold text-muted small d-block mb-1">Customer Details</span>
                    <h5 class="fw-bold mb-1 text-dark">{{ $repair->customer_name }}</h5>
                    <div class="small text-muted"><strong>Phone:</strong> {{ $repair->customer_phone }}</div>
                    <div class="small text-muted"><strong>Repair Status:</strong> 
                        <span class="badge {{ $repair->status == 'Delivered' ? 'bg-success' : 'bg-primary' }}">{{ $repair->status }}</span>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-uppercase fw-bold text-muted small d-block mb-1">Device & Service Info</span>
                    <h5 class="fw-bold mb-1 text-dark">{{ $repair->device_name }}</h5>
                    <div class="small text-muted"><strong>Expected Delivery:</strong> {{ $repair->expected_delivery ? $repair->expected_delivery->format('d M, Y') : 'Pending' }}</div>
                    <div class="small text-muted"><strong>Technician:</strong> {{ $repair->handler->name ?? 'Altamash Technician' }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="p-2 px-3 bg-light rounded border">
                    <strong class="small text-dark">Reported Fault / Problem:</strong>
                    <span class="small text-secondary ms-1">{{ $repair->problem_description }}</span>
                </div>
            </div>
        </div>

        <!-- Materials / Spare Parts Table -->
        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-primary-custom">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>Material / Spare Part Description</th>
                        <th class="text-center" style="width: 15%;">Qty</th>
                        <th class="text-end" style="width: 20%;">Unit Cost (₹)</th>
                        <th class="text-end" style="width: 20%;">Total Cost (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repair->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $item->item_name }}</div>
                            @if($item->product)
                                <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                            @endif
                        </td>
                        <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                        <td class="text-end">₹{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-2 text-muted small">Standard general servicing without external spare parts replaced.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals & Notes -->
        <div class="row g-3">
            <div class="col-7">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-uppercase fw-bold text-muted small d-block mb-1">Terms & Warranty Terms</span>
                    <ul class="small text-muted ps-3 mb-0">
                        <li>30 days testing warranty on genuine replaced parts.</li>
                        <li>No warranty on physical damage, liquid ingress, or tampering.</li>
                        <li>Please present this slip when collecting your device.</li>
                        <li>Devices not collected within 60 days are subject to disposal.</li>
                    </ul>
                </div>
            </div>
            <div class="col-5">
                <table class="table table-bordered totals-table mb-0">
                    <tr>
                        <td class="text-muted">Parts / Material Cost:</td>
                        <td class="text-end fw-semibold">₹{{ number_format($repair->material_cost, 2) }}</td>
                    </tr>
                    @if($repair->service_charge > 0)
                    <tr>
                        <td class="text-muted">Service / Labor Fee:</td>
                        <td class="text-end fw-semibold">₹{{ number_format($repair->service_charge, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>Total Bill Payable:</td>
                        <td class="text-end">₹{{ number_format($repair->effective_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-success fw-semibold">Amount Paid:</td>
                        <td class="text-end text-success fw-bold">₹{{ number_format($repair->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="due-row">
                        <td>Pending Due Balance:</td>
                        <td class="text-end">₹{{ number_format($repair->due_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="row signature-area">
            <div class="col-6 text-center">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 35px auto 5px;"></div>
                <small class="text-muted fw-semibold">Customer Signature</small>
            </div>
            <div class="col-6 text-center">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 35px auto 5px;"></div>
                <small class="text-muted fw-semibold">Authorized Signatory (ALTAMASH MOBILE)</small>
            </div>
        </div>
    </div>
</body>
</html>
