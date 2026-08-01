<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase #{{ $purchase->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #0d6efd;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            color: #0d6efd;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 10px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            font-weight: bold;
            background: #f5f5f5;
            width: 30%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th {
            background: #0d6efd;
            color: #fff;
            padding: 8px;
            text-align: left;
            border: 1px solid #0d6efd;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        .items-table tfoot td {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        .total-row td {
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #666;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #198754;
        }
        .text-danger {
            color: #dc3545;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background: #d1e7dd;
            color: #0a3622;
        }
        .badge-warning {
            background: #fff3cd;
            color: #664d03;
        }
        .badge-danger {
            background: #f8d7da;
            color: #58151c;
        }
        .badge-info {
            background: #cff4fc;
            color: #055160;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $settings['shop_name'] ?? 'Altamash Mobiles' }}</h1>
        <p>{{ $settings['shop_address'] ?? '' }}</p>
        <p>Phone: {{ $settings['shop_phone'] ?? '' }} | Email: {{ $settings['shop_email'] ?? '' }}</p>
        <p>GST: {{ $settings['gst_number'] ?? '' }}</p>
    </div>

    <div class="title">PURCHASE ORDER</div>

    <table class="info-table">
        <tr>
            <td class="label">Invoice Number</td>
            <td><strong>{{ $purchase->invoice_number }}</strong></td>
            <td class="label">Purchase Date</td>
            <td>{{ $purchase->purchase_date->format('d M, Y') }}</td>
        </tr>
        <tr>
            <td class="label">Supplier</td>
            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
            <td class="label">Status</td>
            <td>
                @if($purchase->status == 'completed')
                    <span class="badge badge-success">Completed</span>
                @else
                    <span class="badge badge-warning">Pending</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">Supplier Phone</td>
            <td>{{ $purchase->supplier->phone ?? 'N/A' }}</td>
            <td class="label">Payment Status</td>
            <td>
                @if($purchase->payment_status == 'paid')
                    <span class="badge badge-success">Paid</span>
                @elseif($purchase->payment_status == 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Pending</span>
                @endif
            </td>
        </tr>
        @if($purchase->notes)
        <tr>
            <td class="label">Notes</td>
            <td colspan="3">{{ $purchase->notes }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>Item</th>
                <th style="width: 100px;">Type</th>
                <th style="width: 60px;">Qty</th>
                <th style="width: 120px;">Purchase Price</th>
                <th style="width: 120px;">Selling Price</th>
                <th style="width: 130px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->details as $detail)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->purchasable->name ?? 'N/A' }}</td>
                    <td>{{ class_basename($detail->purchasable_type) }}</td>
                    <td class="text-center">{{ $detail->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($detail->purchase_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($detail->selling_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($detail->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Subtotal:</td>
                <td class="text-right">Rs. {{ number_format($purchase->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Discount:</td>
                <td class="text-right text-success">- Rs. {{ number_format($purchase->discount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">GST (18%):</td>
                <td class="text-right">Rs. {{ number_format($purchase->gst_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Amount:</td>
                <td class="text-right">Rs. {{ number_format($purchase->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Paid Amount:</td>
                <td class="text-right text-success">Rs. {{ number_format($purchase->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Due Amount:</td>
                <td class="text-right text-danger">Rs. {{ number_format($purchase->due_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <p><strong>Supplier Signature:</strong></p>
                    <p style="margin-top: 40px;">_________________________</p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <p><strong>Authorized Signature:</strong></p>
                    <p style="margin-top: 40px;">_________________________</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Generated on: {{ now()->format('d M, Y H:i:s') }}</p>
        <p>This is a computer generated invoice.</p>
    </div>
</body>
</html>