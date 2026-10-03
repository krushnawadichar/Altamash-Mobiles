<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <!-- Bootstrap CSS for basic styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            background-color: #e9ecef;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .a4-page {
            width: 210mm;
            min-height: 296mm; /* slightly less than 297mm to prevent overflow */
            padding: 10mm 15mm; /* Reduced padding */
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            box-sizing: border-box;
        }
        .invoice-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .invoice-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0d6efd;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .table > :not(caption) > * > * {
            padding: 8px 10px; /* Reduced padding */
        }
        .table-primary th {
            background-color: #0d6efd !important;
            color: white !important;
        }
        .totals-table td {
            padding: 5px 15px; /* Reduced padding */
            font-size: 1rem; /* Slightly smaller font */
        }
        .grand-total-row {
            background-color: #f1f8ff !important;
            font-weight: bold;
            font-size: 1.2rem;
            color: #0d6efd;
        }
        .footer-bottom {
            position: absolute; 
            bottom: 10mm; /* Reduced bottom margin */
            width: calc(100% - 30mm);
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
                page-break-after: avoid; /* Prevent forced page breaks */
            }
            /* Hide the print button when printing */
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container text-center my-3 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-printer me-2" viewBox="0 0 16 16"><path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/><path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/></svg> Print Invoice</button>
        <button onclick="window.close()" class="btn btn-secondary btn-lg ms-2">Close</button>
    </div>

    <div class="a4-page">
        <!-- Header -->
        <div class="row invoice-header align-items-center">
            <div class="col-6">
                <h2 class="fw-bolder mb-1 text-dark">ALTAMASH MOBILE</h2>
                <p class="text-muted mb-0">New narsala road, <br>opposite Dhanashree apartment</p>
                <p class="text-muted mb-0">Narsala, Nagpur, Maharashtra, 440034.</p>
                <p class="text-muted mb-0">Phone: 8956586537</p>
                <p class="text-muted mb-0">Email: info@ALTAMASH MOBILE.com</p>
            </div>
            <div class="col-6 text-end">
                <div class="invoice-title">INVOICE</div>
                <h5 class="mb-1 text-dark"># {{ $sale->invoice_number }}</h5>
                <p class="mb-0 text-muted"><strong>Date:</strong> {{ $sale->sale_date->format('d M, Y') }}</p>
                <p class="mb-0 text-muted"><strong>Status:</strong> 
                    @if($sale->payment_status == 'paid')
                        <span class="text-success fw-bold">PAID</span>
                    @elseif($sale->payment_status == 'partial')
                        <span class="text-warning fw-bold">PARTIAL</span>
                    @else
                        <span class="text-danger fw-bold">UNPAID</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Billing Info -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="p-3 bg-light rounded border border-light-subtle">
                    <h6 class="fw-bold text-uppercase text-primary mb-2" style="letter-spacing: 1px;">Billed To:</h6>
                    @if($sale->customer)
                        <h5 class="fw-bold mb-1">{{ $sale->customer->name }}</h5>
                        @if($sale->customer->address)<p class="mb-0">{{ $sale->customer->address }}</p>@endif
                        @if($sale->customer->city || $sale->customer->state)
                            <p class="mb-0">{{ $sale->customer->city ?? '' }}{{ $sale->customer->state ? ', ' . $sale->customer->state : '' }}</p>
                        @endif
                        <p class="mb-0">Phone: {{ $sale->customer->phone ?? 'N/A' }}</p>
                    @else
                        <h5 class="fw-bold mb-0">Walk-in Customer</h5>
                    @endif
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-bordered border-secondary mb-5">
            <thead class="table-primary border-primary">
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="45%">Item Description</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="15%" class="text-end">Unit Price</th>
                    <th width="20%" class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="fw-bold">{{ $item->product->name }}</span>
                        @if($item->product->barcode)
                        <br><small class="text-muted">SKU: {{ $item->product->barcode }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="row">
            <div class="col-6">
                <h6 class="fw-bold text-uppercase text-muted">Notes / Terms:</h6>
                <p class="text-muted small">
                    {{ $sale->notes ?? 'Thank you for your business! Goods once sold cannot be returned without original invoice. Warranty subject to manufacturer terms.' }}
                </p>
            </div>
            <div class="col-6">
                <table class="table table-borderless totals-table mb-0">
                    <tr>
                        <td class="text-end text-muted">Subtotal:</td>
                        <td class="text-end fw-semibold">₹{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->discount > 0)
                    <tr>
                        <td class="text-end text-muted">Discount:</td>
                        <td class="text-end text-danger fw-semibold">- ₹{{ number_format($sale->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($sale->tax > 0)
                    <tr>
                        <td class="text-end text-muted">Tax:</td>
                        <td class="text-end fw-semibold">₹{{ number_format($sale->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row border-top border-primary border-2 mt-2">
                        <td class="text-end pt-3 pb-3">Grand Total:</td>
                        <td class="text-end pt-3 pb-3">₹{{ number_format($sale->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end text-muted pt-3">Amount Paid ({{ $sale->payment_method }}):</td>
                        <td class="text-end text-success fw-bold pt-3">₹{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end text-muted">Balance Due:</td>
                        <td class="text-end text-danger fw-bold">₹{{ number_format($sale->due_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer-bottom border-top text-muted pt-4">
            <div class="row">
                <div class="col-6 text-start">
                    <p class="mb-0 small">Prepared By: <br><strong>{{ $sale->creator->name ?? 'Admin' }}</strong></p>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-0 small">Authorized Signatory</p>
                    <div style="border-bottom: 1px solid #333; width: 150px; margin-left: auto; margin-top: 30px;"></div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0" style="font-size: 0.8rem;">This is a computer-generated invoice.</p>
            </div>
        </div>
    </div>
</body>
</html>


