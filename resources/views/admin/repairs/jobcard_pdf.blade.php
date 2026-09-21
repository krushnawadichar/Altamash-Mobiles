<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Card - {{ $repair->repair_no }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .shop-title { font-size: 18px; font-weight: bold; color: #4338ca; }
        .border-box { border: 1px solid #cbd5e1; padding: 8px; border-radius: 4px; background: #f8fafc; }
        .fw-bold { font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table class="table" style="border-bottom: 2px solid #cbd5e1; padding-bottom: 10px;">
        <tr>
            <td style="width: 60%;">
                <div class="shop-title">{{ $settings['shop_name'] }}</div>
                <div>{{ $settings['shop_address'] }}</div>
                <div>Phone: {{ $settings['shop_phone'] }} | Email: {{ $settings['shop_email'] }}</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div style="background: #4338ca; color: #fff; padding: 4px 8px; display: inline-block; font-weight: bold;">REPAIR JOB CARD</div>
                <div style="font-size: 13px; font-weight: bold; margin-top: 5px;">{{ $repair->repair_no }}</div>
                <div>Date: {{ $repair->received_date->format('d M Y, h:i A') }}</div>
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
                    <div style="font-weight: bold; color: #64748b; font-size: 9px; text-transform: uppercase;">Device:</div>
                    <div style="font-size: 12px; font-weight: bold;">{{ $repair->model_name }} ({{ $repair->color ?: 'Standard' }})</div>
                    <div>IMEI / Serial: {{ $repair->imei ?: $repair->serial_no ?: 'N/A' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="border-box" style="margin-bottom: 15px;">
        <div style="font-weight: bold; color: #dc2626;">Reported Defect / Complaint:</div>
        <div>{{ $repair->problem_complaint }}</div>
        <div style="margin-top: 6px; font-size: 10px; color: #64748b;">
            Physical Condition: {{ $repair->physical_condition ?: 'Normal wear' }} | Accessories: {{ $repair->accessories_received ?: 'Device only' }}
        </div>
    </div>

    <table class="table">
        <tr>
            <td style="text-align: center; border: 1px solid #cbd5e1; padding: 6px;">
                <div>Estimated Cost</div>
                <div style="font-weight: bold; font-size: 13px;">₹{{ number_format($repair->estimated_cost, 2) }}</div>
            </td>
            <td style="text-align: center; border: 1px solid #cbd5e1; padding: 6px;">
                <div>Advance Received</div>
                <div style="font-weight: bold; font-size: 13px; color: #16a34a;">₹{{ number_format($repair->advance_amount, 2) }}</div>
            </td>
            <td style="text-align: center; border: 1px solid #cbd5e1; padding: 6px;">
                <div>Expected Delivery</div>
                <div style="font-weight: bold; font-size: 13px;">{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d M Y') : 'TBD' }}</div>
            </td>
        </tr>
    </table>

    <div style="font-size: 9px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 10px; margin-top: 20px;">
        <div style="font-weight: bold; margin-bottom: 4px;">Terms:</div>
        <div>1. Present this Job Card to collect your device. 2. Not responsible for personal data loss. 3. Warranty: {{ $repair->warranty_days }} days on replaced parts.</div>
        
        <table style="width: 100%; margin-top: 40px;">
            <tr>
                <td style="text-align: center; border-top: 1px solid #999; width: 40%;">Customer Signature</td>
                <td style="width: 20%;"></td>
                <td style="text-align: center; border-top: 1px solid #999; width: 40%;">Authorized Signatory</td>
            </tr>
        </table>
    </div>
</body>
</html>
