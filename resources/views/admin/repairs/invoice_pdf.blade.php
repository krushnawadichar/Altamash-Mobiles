<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Repair Invoice - {{ $repair->repair_no }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .shop-title { font-size: 18px; font-weight: bold; color: #16a34a; }
        .border-box { border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: #f8fafc; }
        .fw-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .items-table th { background: #f1f5f9; padding: 6px; text-align: left; border-bottom: 2px solid #cbd5e1; }
        .items-table td { padding: 6px; border-bottom: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <table class="table" style="border-bottom: 2px solid #cbd5e1; padding-bottom: 10px;">
        <tr>
            <td style="width: 60%;">
                <div class="shop-title">{{ $settings['shop_name'] }}</div>
                <div>{{ $settings['shop_address'] }}</div>
                <div>Phone: {{ $settings['shop_phone'] }} | Email: {{ $settings['shop_email'] }}</div>
                @if(!empty($settings['gst_number']))
                    <div>GSTIN: {{ $settings['gst_number'] }}</div>
                @endif
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="background: #16a34a; color: #fff; padding: 4px 8px; display: inline-block; font-weight: bold;">REPAIR BILL</div>
                <div style="font-size: 13px; font-weight: bold; margin-top: 5px;">{{ $repair->repair_no }}</div>
                <div>Date: {{ date('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="table">
        <tr>
            <td style="width: 50%; padding-right: 5px;">
                <div class="border-box">
                    <div style="font-weight: bold; color: #64748b; font-size: 9px; text-transform: uppercase;">Customer:</div>
                    <div style="font-size: 12px; font-weight: bold;">{{ $repair->customer_name }}</div>
                    <div>Phone: +91 {{ $repair->customer_mobile }}</div>
                </div>
            </td>
            <td style="width: 50%; padding-left: 5px;">
                <div class="border-box">
                    <div style="font-weight: bold; color: #64748b; font-size: 9px; text-transform: uppercase;">Serviced Device:</div>
                    <div style="font-size: 12px; font-weight: bold;">{{ $repair->model_name }}</div>
                    <div>IMEI / Serial: {{ $repair->imei ?: $repair->serial_no ?: 'N/A' }}</div>
                    <div style="color: #16a34a; font-weight: bold;">Warranty: {{ $repair->warranty_days }} Days</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="table items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 60%;">Service / Spare Part Description</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 25%; text-align: right;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @foreach($repair->partsUsed as $part)
            <tr>
                <td>{{ $idx++ }}</td>
                <td>
                    <div class="fw-bold">{{ $part->product->name }}</div>
                    <div style="font-size: 9px; color: #64748b;">Replacement Spare Part</div>
                </td>
                <td class="text-center">{{ $part->quantity }}</td>
                <td class="text-right fw-bold">{{ number_format($part->subtotal, 2) }}</td>
            </tr>
            @endforeach

            @php
                $partsTotal = $repair->partsUsed->sum('subtotal');
                $labor = max(0, $repair->final_cost - $partsTotal);
            @endphp
            @if($labor > 0 || $repair->partsUsed->isEmpty())
            <tr>
                <td>{{ $idx }}</td>
                <td>
                    <div class="fw-bold">Technical Diagnostics & Servicing Labor</div>
                </td>
                <td class="text-center">1</td>
                <td class="text-right fw-bold">{{ number_format($labor, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <table class="table" style="margin-top: 10px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding: 3px 0; font-size: 13px; font-weight: bold;">Total Bill:</td>
                        <td style="text-align: right; font-size: 13px; font-weight: bold; color: #16a34a;">₹{{ number_format($repair->final_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 3px 0;">Paid (Advance + Final):</td>
                        <td style="text-align: right;">₹{{ number_format($repair->paid_amount, 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #333;">
                        <td style="padding: 4px 0; font-weight: bold;">Balance:</td>
                        <td style="text-align: right; font-weight: bold; color: {{ $repair->due_amount > 0 ? '#dc2626' : '#16a34a' }};">
                            ₹{{ number_format($repair->due_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 40px; border-top: 1px solid #cbd5e1; padding-top: 10px;">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: center; border-top: 1px solid #999; width: 40%;">Customer Signature</td>
                <td style="width: 20%;"></td>
                <td style="text-align: center; border-top: 1px solid #999; width: 40%;">Authorized Signatory</td>
            </tr>
        </table>
    </div>
</body>
</html>
