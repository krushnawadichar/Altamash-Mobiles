@extends('layouts.admin')

@section('title', 'Invoice #' . $sale->invoice_no)
@section('page_title', 'Tax Invoice')

@section('content')
<style>
    /* Invoice Styles */
    .invoice-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        max-width: 820px;
        margin: 0 auto;
        padding: 40px;
        border: 1px solid #e2e8f0;
    }

    .thermal-receipt {
        display: none;
        width: 80mm;
        margin: 0 auto;
        padding: 10px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        line-height: 1.3;
        color: #000;
        background: #fff;
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

        /* Default A4 Print */
        body.print-a4 .invoice-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }
        body.print-a4 .thermal-receipt {
            display: none !important;
        }

        /* Thermal 80mm Print */
        body.print-thermal .invoice-card {
            display: none !important;
        }
        body.print-thermal .thermal-receipt {
            display: block !important;
            width: 80mm !important;
            margin: 0 auto !important;
            padding: 5px !important;
        }
    }
</style>

<div class="container-fluid px-0">

    <!-- Top Action Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
        <div>
            <h4 class="fw-bold mb-0">Invoice: {{ $sale->invoice_no }}</h4>
            <span class="badge {{ $sale->payment_badge_class }}">{{ ucfirst($sale->payment_status) }}</span>
            <span class="badge bg-light text-dark border ms-1">{{ $sale->sale_date->format('d M Y') }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button onclick="printA4()" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Print A4 Invoice
            </button>
            <button onclick="printThermal()" class="btn btn-dark btn-sm fw-bold">
                <i class="bi bi-receipt me-1"></i> 80mm Thermal Receipt
            </button>
            <a href="{{ route('admin.pos.pdf', $sale) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF
            </a>
            @php
                $whatsappText = urlencode("Hello {$sale->customer_name}, thank you for your purchase from {$settings['shop_name']}! Invoice #{$sale->invoice_no} Total: ₹" . number_format($sale->grand_total, 2));
                $waPhone = preg_replace('/[^0-9]/', '', $sale->customer_mobile ?? '');
            @endphp
            @if(!empty($waPhone))
            <a href="https://wa.me/91{{ $waPhone }}?text={{ $whatsappText }}" target="_blank" class="btn btn-success btn-sm">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
            </a>
            @endif
            <a href="{{ route('admin.pos.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to POS
            </a>
        </div>
    </div>

    <!-- Standard A4 Invoice Layout -->
    <div class="invoice-card" id="a4InvoiceSection">
        <!-- Header -->
        <div class="row align-items-center mb-4 border-bottom pb-4">
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
                <span class="badge bg-primary text-uppercase px-3 py-2 fs-6 mb-2">TAX INVOICE</span>
                <h5 class="fw-bold mb-1">{{ $sale->invoice_no }}</h5>
                <div class="text-muted small">Date: <strong>{{ $sale->sale_date->format('d M Y') }}</strong></div>
                <div class="text-muted small">Billed By: {{ $sale->creator?->name ?? 'Sales Staff' }}</div>
            </div>
        </div>

        <!-- Billed To -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.05em;">Billed To:</h6>
                <div class="fw-bold fs-6">{{ $sale->customer_name ?: 'Walk-in Customer' }}</div>
                @if($sale->customer_mobile)
                    <div class="text-muted small"><i class="bi bi-telephone me-1"></i>+91 {{ $sale->customer_mobile }}</div>
                @endif
                @if($sale->customer?->address)
                    <div class="text-muted small">{{ $sale->customer->address }}</div>
                @endif
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.05em;">Payment Details:</h6>
                <div><strong>Payment Method:</strong> <span class="text-uppercase">{{ $sale->payment_method }}</span></div>
                <div><strong>Payment Status:</strong> <span class="badge {{ $sale->payment_badge_class }}">{{ ucfirst($sale->payment_status) }}</span></div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 50%;">Item Description</th>
                        <th class="text-center" style="width: 15%;">Unit Price</th>
                        <th class="text-center" style="width: 10%;">Qty</th>
                        <th class="text-end" style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                            <div class="text-muted small">
                                SKU: <code>{{ $item->product->sku }}</code>
                                @if($item->imei)
                                    &bull; <strong class="text-primary">IMEI: {{ $item->imei }}</strong>
                                @endif
                                @if($item->warranty_months > 0)
                                    &bull; <span>Warranty: {{ $item->warranty_months }} Months</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Calculation -->
        <div class="row justify-content-end mb-4">
            <div class="col-sm-6 col-md-5">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-semibold">₹{{ number_format($sale->subtotal, 2) }}</span>
                </div>
                @if($sale->discount_amount > 0)
                <div class="d-flex justify-content-between mb-2 text-danger">
                    <span>Discount:</span>
                    <span class="fw-semibold">- ₹{{ number_format($sale->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($sale->tax_amount > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">GST / Tax:</span>
                    <span class="fw-semibold">₹{{ number_format($sale->tax_amount, 2) }}</span>
                </div>
                @endif
                <hr class="my-2">
                <div class="d-flex justify-content-between mb-2 fs-5">
                    <span class="fw-bold">Grand Total:</span>
                    <span class="fw-bold text-primary">₹{{ number_format($sale->grand_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Paid Amount:</span>
                    <span class="fw-bold">₹{{ number_format($sale->paid_amount, 2) }}</span>
                </div>
                @if($sale->due_amount > 0)
                <div class="d-flex justify-content-between text-danger fw-bold">
                    <span>Balance Due:</span>
                    <span>₹{{ number_format($sale->due_amount, 2) }}</span>
                </div>
                @endif
                @if($sale->change_amount > 0)
                <div class="d-flex justify-content-between text-secondary">
                    <span>Change Returned:</span>
                    <span>₹{{ number_format($sale->change_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Terms & Footer -->
        <div class="border-top pt-4 text-muted small">
            <div class="row">
                <div class="col-sm-8">
                    <h6 class="fw-bold text-dark small mb-1">Terms & Conditions:</h6>
                    <div style="font-size: 0.78rem; line-height: 1.4; white-space: pre-line;">
                        {{ $settings['terms_conditions'] }}
                    </div>
                </div>
                <div class="col-sm-4 text-sm-end mt-4 mt-sm-0">
                    <div class="pt-5 border-top d-inline-block text-center" style="min-width: 140px;">
                        <strong>Authorized Signatory</strong>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4 pt-3 border-top" style="font-size: 0.78rem;">
                {{ $settings['invoice_footer'] }}
            </div>
        </div>
    </div>

    <!-- 80mm Thermal Receipt Layout (Hidden on Screen, Active when thermal print selected) -->
    <div class="thermal-receipt" id="thermalReceiptSection">
        <div style="text-align: center; margin-bottom: 8px;">
            <div style="font-size: 15px; font-weight: bold;">{{ $settings['shop_name'] }}</div>
            <div style="font-size: 10px;">{{ $settings['shop_address'] }}</div>
            <div style="font-size: 10px;">Ph: {{ $settings['shop_phone'] }}</div>
            @if(!empty($settings['gst_number']))
                <div style="font-size: 10px;">GSTIN: {{ $settings['gst_number'] }}</div>
            @endif
            <div>--------------------------------</div>
            <div style="font-weight: bold;">TAX INVOICE</div>
            <div>--------------------------------</div>
        </div>

        <div>
            <div>Inv: {{ $sale->invoice_no }}</div>
            <div>Date: {{ $sale->sale_date->format('d/m/Y') }}</div>
            <div>Cust: {{ $sale->customer_name ?: 'Walk-in' }} ({{ $sale->customer_mobile ?: '-' }})</div>
            <div>--------------------------------</div>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px dashed #000;">
                    <th style="text-align: left;">Item</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td colspan="4" style="padding-top: 4px; font-weight: bold;">{{ $item->product->name }}</td>
                </tr>
                @if($item->imei)
                <tr>
                    <td colspan="4" style="font-size: 10px;">IMEI: {{ $item->imei }}</td>
                </tr>
                @endif
                <tr style="border-bottom: 1px dashed #ddd;">
                    <td></td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">₹{{ number_format($item->unit_price, 0) }}</td>
                    <td style="text-align: right; font-weight: bold;">₹{{ number_format($item->subtotal, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 8px; border-top: 1px dashed #000; padding-top: 4px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span>
                <span>₹{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Discount:</span>
                <span>-₹{{ number_format($sale->discount_amount, 2) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; margin: 4px 0;">
                <span>TOTAL:</span>
                <span>₹{{ number_format($sale->grand_total, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Paid ({{ strtoupper($sale->payment_method) }}):</span>
                <span>₹{{ number_format($sale->paid_amount, 2) }}</span>
            </div>
            @if($sale->due_amount > 0)
            <div style="display: flex; justify-content: space-between; font-weight: bold;">
                <span>DUE:</span>
                <span>₹{{ number_format($sale->due_amount, 2) }}</span>
            </div>
            @endif
            @if($sale->change_amount > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Change:</span>
                <span>₹{{ number_format($sale->change_amount, 2) }}</span>
            </div>
            @endif
        </div>

        <div style="text-align: center; margin-top: 12px; font-size: 10px; border-top: 1px dashed #000; padding-top: 6px;">
            <div>Thank You! Visit Again.</div>
            <div>{{ $settings['shop_name'] }}</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function printA4() {
        document.body.classList.remove('print-thermal');
        document.body.classList.add('print-a4');
        window.print();
    }

    function printThermal() {
        document.body.classList.remove('print-a4');
        document.body.classList.add('print-thermal');
        window.print();
    }
</script>
@endpush
