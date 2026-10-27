<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_number }} - ALTAMASH MOBILES</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --brand-color: #0d6efd;
            --brand-dark: #0f172a;
        }
        body {
            background-color: #f1f5f9;
            color: #334155;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        /* Sticky Top Action Bar for Customer */
        .customer-topbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 12px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .invoice-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            padding: 35px;
            margin-top: 25px;
            margin-bottom: 40px;
        }

        .shop-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .invoice-pill {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--brand-color);
            letter-spacing: 1px;
        }

        .table-items th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 12px 10px;
            border-bottom: 2px solid #cbd5e1;
        }
        
        .table-items td {
            padding: 12px 10px;
            vertical-align: middle;
            color: #1e293b;
        }

        .totals-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px 20px;
        }

        .grand-total-val {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--brand-color);
        }

        .due-badge {
            font-size: 0.95rem;
            padding: 6px 12px;
        }

        /* Print Specific Styling */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .no-print, .customer-topbar {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .totals-box {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            @page {
                size: A4;
                margin: 12mm 15mm;
            }
        }

        @media (max-width: 768px) {
            .invoice-card {
                padding: 20px 15px;
                border-radius: 0;
                margin-top: 0;
            }
            .shop-title {
                font-size: 1.4rem;
            }
            .invoice-pill {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Customer Sticky Top Action Bar (hidden on print) -->
    <div class="customer-topbar no-print">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2"><i class="fa-solid fa-receipt me-1"></i> Official Invoice</span>
                    <span class="fw-bold text-dark d-none d-sm-inline">{{ $sale->invoice_number }}</span>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="fa-solid fa-file-arrow-down me-1"></i> Download / Print PDF
                    </button>
                    <a href="https://wa.me/918956586537?text={{ urlencode('Hi Altamash Mobiles, I have a query regarding my invoice #' . $sale->invoice_number) }}" 
                       target="_blank" 
                       class="btn btn-success btn-sm fw-semibold px-3">
                        <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Shop
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-3 my-md-4">
        <div class="invoice-card mx-auto" style="max-width: 850px;">
            <!-- Header Section -->
            <div class="row pb-3 mb-3 border-bottom align-items-start">
                <div class="col-sm-7 mb-3 mb-sm-0">
                    <div class="d-flex align-items-center mb-1">
                        <i class="fa-solid fa-mobile-screen-button text-primary fs-2 me-2"></i>
                        <span class="shop-title">ALTAMASH MOBILES</span>
                    </div>
                    <p class="text-muted small mb-1">
                        <i class="fa-solid fa-location-dot me-1 text-danger"></i> New Narsala road, opposite Dhanashree apartment,<br>
                        Narsala, Nagpur, Maharashtra - 440034.
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fa-solid fa-phone me-1 text-success"></i> +91 8956586537 &nbsp;|&nbsp; 
                        <i class="fa-solid fa-envelope me-1 text-primary"></i> support@altamashmobiles.com
                    </p>
                </div>
                <div class="col-sm-5 text-sm-end">
                    <div class="invoice-pill text-uppercase">Tax Invoice</div>
                    <div class="fw-bold text-dark fs-5 mb-1">#{{ $sale->invoice_number }}</div>
                    <div class="text-muted small mb-1">
                        <strong>Date:</strong> {{ $sale->sale_date->format('d M, Y') }}
                    </div>
                    <div>
                        @if($sale->payment_status == 'paid')
                            <span class="badge bg-success due-badge">
                                <i class="fa-solid fa-circle-check me-1"></i> Paid in Full
                            </span>
                        @elseif($sale->payment_status == 'partial')
                            <span class="badge bg-warning text-dark due-badge">
                                <i class="fa-solid fa-circle-half-stroke me-1"></i> Partial Paid (₹{{ number_format($sale->due_amount, 2) }} Due)
                            </span>
                        @else
                            <span class="badge bg-danger due-badge">
                                <i class="fa-solid fa-circle-xmark me-1"></i> Unpaid / Credit
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer & Payment Details -->
            <div class="row mb-4 g-3">
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded border h-100">
                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">
                            <i class="fa-solid fa-user me-1 text-primary"></i> Billed To:
                        </span>
                        @if($sale->customer)
                            <h6 class="fw-bold text-dark mb-1">{{ $sale->customer->name }}</h6>
                            @if($sale->customer->phone)
                                <div class="small text-muted mb-1">
                                    <i class="fa-solid fa-phone text-muted me-1"></i> {{ $sale->customer->phone }}
                                </div>
                            @endif
                            @if($sale->customer->address)
                                <div class="small text-muted">{{ $sale->customer->address }}</div>
                            @endif
                            @if($sale->customer->city)
                                <div class="small text-muted">{{ $sale->customer->city }}</div>
                            @endif
                        @else
                            <h6 class="fw-bold text-dark mb-1">Walk-in Customer</h6>
                            <span class="small text-muted">Retail Counter Sale</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 bg-light rounded border h-100">
                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">
                            <i class="fa-solid fa-wallet me-1 text-primary"></i> Payment Details:
                        </span>
                        <div class="small mb-1">
                            <strong>Payment Mode:</strong> 
                            <span class="badge bg-white text-dark border">{{ $sale->payment_method ?? 'Cash' }}</span>
                        </div>
                        @if($sale->notes)
                        <div class="small text-muted mb-1">
                            <strong>Payment Notes / Ref:</strong> {{ $sale->notes }}
                        </div>
                        @endif
                        <div class="small text-muted">
                            <strong>Served by:</strong> {{ $sale->creator->name ?? 'Store Cashier' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-items align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 5%;" class="text-center">#</th>
                            <th style="width: 50%;">Item & Description</th>
                            <th style="width: 15%;" class="text-end">Unit Price</th>
                            <th style="width: 10%;" class="text-center">Qty</th>
                            <th style="width: 20%;" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $index => $item)
                        <tr>
                            <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->product->name ?? 'Product' }}</div>
                                @if(isset($item->product->sku) && $item->product->sku)
                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                @endif
                            </td>
                            <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary & Totals -->
            <div class="row justify-content-end mb-4">
                <div class="col-md-6 col-lg-5">
                    <div class="totals-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">₹{{ number_format($sale->subtotal, 2) }}</span>
                        </div>
                        @if($sale->discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Discount:</span>
                            <span>-₹{{ number_format($sale->discount, 2) }}</span>
                        </div>
                        @endif
                        @if($sale->tax > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax (GST):</span>
                            <span>₹{{ number_format($sale->tax, 2) }}</span>
                        </div>
                        @endif
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold text-dark fs-5">Grand Total:</span>
                            <span class="grand-total-val">₹{{ number_format($sale->grand_total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-success mb-2">
                            <span class="fw-semibold">Amount Paid:</span>
                            <span class="fw-bold">₹{{ number_format($sale->paid_amount, 2) }}</span>
                        </div>
                        @if($sale->due_amount > 0)
                        <div class="d-flex justify-content-between text-danger pt-2 border-top">
                            <span class="fw-bold">Pending Due Balance:</span>
                            <span class="fw-bold fs-6">₹{{ number_format($sale->due_amount, 2) }}</span>
                        </div>
                        @else
                        <div class="d-flex justify-content-between text-muted pt-2 border-top small">
                            <span>Balance Due:</span>
                            <span class="text-success fw-bold">₹0.00 (Nil)</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer & Terms -->
            <div class="border-top pt-3 text-center text-muted small">
                <p class="mb-1 fw-semibold text-dark">Thank you for choosing ALTAMASH MOBILES!</p>
                <p class="mb-0 text-muted">Please retain this digital invoice for warranty validation and repair assistance. Goods once sold are subject to manufacturer warranty terms.</p>
            </div>
        </div>
    </div>

</body>
</html>
