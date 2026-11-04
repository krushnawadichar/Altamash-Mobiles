<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Voucher - {{ $purchase->invoice_number ?? 'PO-#'.$purchase->id }}</title>
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
        .voucher-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .voucher-title {
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
            margin-top: 40px;
            padding-top: 20px;
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
            </svg> Print Purchase Voucher
        </button>
        <button onclick="window.close()" class="btn btn-secondary px-4 ms-2">Close</button>
    </div>

    <div class="a4-page">
        <!-- Header -->
        <div class="row voucher-header align-items-center">
            <div class="col-7">
                <h2 class="fw-bold mb-1 text-dark">ALTAMASH MOBILE</h2>
                <p class="text-muted small mb-0">
                    New narsala road, opposite Dhanashree apartment<br>
                    <strong>Phone:</strong> +91 88884 94747 | <strong>Email:</strong> altamashmobile1@gmail.com
                </p>
            </div>
            <div class="col-5 text-end">
                <div class="voucher-title">PURCHASE BILL</div>
                <div class="fw-bold text-dark mt-1">Invoice #: {{ $purchase->invoice_number ?? 'PO-#'.$purchase->id }}</div>
                <div class="text-muted small">Date: {{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y h:i A') : 'N/A' }}</div>
            </div>
        </div>

        <!-- Supplier & Payment Meta -->
        <div class="row mb-3">
            <div class="col-7">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-uppercase fw-bold text-muted small d-block mb-1">Supplier Details</span>
                    <h5 class="fw-bold mb-1 text-dark">{{ $purchase->supplier->name ?? 'N/A' }}</h5>
                    @if(!empty($purchase->supplier->company_name))
                        <div class="small fw-semibold text-secondary">{{ $purchase->supplier->company_name }}</div>
                    @endif
                    @if(!empty($purchase->supplier->phone))
                        <div class="small text-muted"><strong>Phone:</strong> {{ $purchase->supplier->phone }}</div>
                    @endif
                    @if(!empty($purchase->supplier->email))
                        <div class="small text-muted"><strong>Email:</strong> {{ $purchase->supplier->email }}</div>
                    @endif
                    @if(!empty($purchase->supplier->address))
                        <div class="small text-muted"><strong>Address:</strong> {{ $purchase->supplier->address }}</div>
                    @endif
                </div>
            </div>
            <div class="col-5">
                <div class="p-3 bg-light rounded border h-100">
                    <span class="text-uppercase fw-bold text-muted small d-block mb-1">Voucher Status</span>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Payment Status:</span>
                        <span class="badge {{ $purchase->payment_status == 'paid' ? 'bg-success' : ($purchase->payment_status == 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ strtoupper($purchase->payment_status ?? 'UNPAID') }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small text-muted">Primary Method:</span>
                        <span class="small fw-bold text-dark">{{ $purchase->payment_method ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Due Balance:</span>
                        <span class="small fw-bold {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                            ₹{{ number_format($purchase->due_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Items Table -->
        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle">
                <thead class="table-primary-custom">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>Product & Specification</th>
                        <th class="text-center" style="width: 15%;">Unit Cost</th>
                        <th class="text-center" style="width: 12%;">Qty</th>
                        <th class="text-end" style="width: 18%;">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchase->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $item->product->name ?? 'Product Deleted' }}</div>
                            @if(!empty($item->product->sku))
                                <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                            @endif
                        </td>
                        <td class="text-center">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">No line items recorded for this purchase.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals & Notes -->
        <div class="row g-3">
            <div class="col-7">
                @if(!empty($purchase->notes))
                <div class="p-3 bg-light rounded border">
                    <span class="text-uppercase fw-bold text-muted small d-block mb-1">Payment & Audit Trail Notes</span>
                    <pre class="small text-secondary mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $purchase->notes }}</pre>
                </div>
                @endif
            </div>
            <div class="col-5">
                <table class="table table-bordered totals-table mb-0">
                    <tr>
                        <td class="text-muted">Subtotal:</td>
                        <td class="text-end fw-semibold">₹{{ number_format($purchase->subtotal, 2) }}</td>
                    </tr>
                    @if($purchase->tax > 0)
                    <tr>
                        <td class="text-muted">GST / Tax:</td>
                        <td class="text-end">₹{{ number_format($purchase->tax, 2) }}</td>
                    </tr>
                    @endif
                    @if($purchase->discount > 0)
                    <tr>
                        <td class="text-muted">Discount:</td>
                        <td class="text-end text-success">-₹{{ number_format($purchase->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>Grand Total:</td>
                        <td class="text-end">₹{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-success fw-semibold">Total Paid:</td>
                        <td class="text-end text-success fw-bold">₹{{ number_format($purchase->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="due-row">
                        <td>Balance Due:</td>
                        <td class="text-end">₹{{ number_format($purchase->due_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="row signature-area">
            <div class="col-6 text-center">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 40px auto 5px;"></div>
                <small class="text-muted fw-semibold">Supplier Authorized Signatory</small>
            </div>
            <div class="col-6 text-center">
                <div style="border-top: 1px dashed #64748b; width: 80%; margin: 40px auto 5px;"></div>
                <small class="text-muted fw-semibold">For ALTAMASH MOBILE (Receiver)</small>
            </div>
        </div>
    </div>
</body>
</html>
