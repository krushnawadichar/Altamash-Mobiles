<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $sale->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .shop-name {
            font-size: 20px;
            font-weight: bold;
            color: #4338ca;
        }
        .badge-title {
            background-color: #4338ca;
            color: #fff;
            padding: 4px 10px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .items-table th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #cbd5e1;
            padding: 8px 6px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 6px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #64748b; font-size: 10px; }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <table class="header-table" style="margin-bottom: 25px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="shop-name">{{ $settings['shop_name'] }}</div>
                <div style="color: #666; margin-bottom: 6px;">{{ $settings['shop_tagline'] }}</div>
                <div>{{ $settings['shop_address'] }}</div>
                <div>Phone: {{ $settings['shop_phone'] }} | Email: {{ $settings['shop_email'] }}</div>
                @if(!empty($settings['gst_number']))
                    <div><strong>GSTIN:</strong> {{ $settings['gst_number'] }}</div>
                @endif
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div class="badge-title">TAX INVOICE</div>
                <div style="font-size: 14px; font-weight: bold; margin-top: 8px;">{{ $sale->invoice_no }}</div>
                <div>Date: <strong>{{ $sale->sale_date->format('d M Y') }}</strong></div>
                <div>Billed By: {{ $sale->creator?->name ?? 'Sales Staff' }}</div>
            </td>
        </tr>
    </table>

    <table class="header-table" style="margin-bottom: 20px; background-color: #f8fafc; padding: 10px; border: 1px solid #e2e8f0;">
        <tr>
            <td style="width: 50%;">
                <div style="font-weight: bold; font-size: 10px; text-transform: uppercase; color: #64748b;">Billed To:</div>
                <div style="font-size: 13px; font-weight: bold;">{{ $sale->customer_name ?: 'Walk-in Customer' }}</div>
                @if($sale->customer_mobile)
                    <div>Phone: +91 {{ $sale->customer_mobile }}</div>
                @endif
                @if($sale->customer?->address)
                    <div>Address: {{ $sale->customer->address }}</div>
                @endif
            </td>
            <td style="width: 50%; text-align: right;">
                <div>Payment Method: <strong>{{ strtoupper($sale->payment_method) }}</strong></div>
                <div>Payment Status: <strong>{{ strtoupper($sale->payment_status) }}</strong></div>
            </td>
        </tr>
    </table>

    <table class="items-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Item & Description</th>
                <th style="width: 15%; text-align: right;">Rate (₹)</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 20%; text-align: right;">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>
                    <div class="fw-bold">{{ $item->product->name }}</div>
                    <div class="text-muted">
                        SKU: {{ $item->product->sku }}
                        @if($item->imei) | IMEI: {{ $item->imei }} @endif
                        @if($item->warranty_months > 0) | Warranty: {{ $item->warranty_months }}M @endif
                    </div>
                </td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right fw-bold">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table" style="margin-bottom: 30px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div style="font-weight: bold; font-size: 11px; margin-bottom: 4px;">Terms & Conditions:</div>
                <div style="font-size: 9px; color: #64748b; white-space: pre-line;">{{ $settings['terms_conditions'] }}</div>
            </td>
            <td style="width: 40%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 3px 0;">Subtotal:</td>
                        <td style="text-align: right; font-weight: bold;">₹{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                    <tr>
                        <td style="padding: 3px 0; color: #dc2626;">Discount:</td>
                        <td style="text-align: right; color: #dc2626;">- ₹{{ number_format($sale->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 1px solid #333;">
                        <td style="padding: 6px 0; font-size: 14px; font-weight: bold;">Grand Total:</td>
                        <td style="text-align: right; font-size: 14px; font-weight: bold; color: #4338ca;">₹{{ number_format($sale->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0; color: #16a34a;">Paid Amount:</td>
                        <td style="text-align: right; color: #16a34a; font-weight: bold;">₹{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    @if($sale->due_amount > 0)
                    <tr>
                        <td style="padding: 3px 0; color: #dc2626; font-weight: bold;">Due Amount:</td>
                        <td style="text-align: right; color: #dc2626; font-weight: bold;">₹{{ number_format($sale->due_amount, 2) }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <div style="text-align: center;">{{ $settings['invoice_footer'] }}</div>
    </div>

</body>
</html>
