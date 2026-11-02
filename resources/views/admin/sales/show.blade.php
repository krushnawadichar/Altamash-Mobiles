@extends('layouts.admin')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-5">
        <h2 class="fw-bold mb-1">Invoice: {{ $sale->invoice_number }}</h2>
        <a href="{{ route('admin.sales.index') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Sales
        </a>
    </div>
    <div class="col-md-7 text-md-end mt-2 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
        <button type="button" 
                class="btn btn-success fw-bold"
                onclick="openWhatsAppModal('{{ $sale->invoice_number }}', '{{ addslashes($sale->customer->name ?? 'Walk-in Customer') }}', '{{ $sale->customer->phone ?? '' }}', '{{ number_format($sale->grand_total, 2) }}', '{{ number_format($sale->paid_amount, 2) }}', '{{ number_format($sale->due_amount, 2) }}', '{{ route('invoice.public', $sale->invoice_number) }}')">
            <i class="fa-brands fa-whatsapp me-1"></i> Send on WhatsApp
        </button>
        @if($sale->due_amount > 0)
            <button type="button" class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#payDueModal">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Receive Due Payment
            </button>
        @endif
        <a href="{{ route('admin.sales.print', $sale->id) }}" target="_blank" class="btn btn-secondary">
            <i class="fa-solid fa-print me-1"></i> Print Invoice
        </a>
    </div>
</div>

@if($sale->due_amount > 0)
    <div class="alert alert-warning border-warning shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <i class="fa-solid fa-triangle-exclamation text-danger fs-5 me-2"></i>
            <strong>Pending Balance:</strong> Customer owes 
            <span class="fw-bold text-danger fs-5">₹{{ number_format($sale->due_amount, 2) }}</span> on this invoice.
        </div>
        <button type="button" class="btn btn-sm btn-success fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#payDueModal">
            <i class="fa-solid fa-money-bill-wave me-1"></i> Clear / Pay Due
        </button>
    </div>
@else
    <div class="alert alert-success border-success shadow-sm mb-4">
        <i class="fa-solid fa-circle-check fs-5 me-2"></i>
        <strong>Paid in Full:</strong> No outstanding balance for this invoice.
    </div>
@endif

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none !important;
            border: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
    }
</style>
@endpush

<div class="card shadow-sm border-0 mb-4" id="print-area">
    <div class="card-body p-4 p-md-5">
        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-1">{{ config('app.name', 'ALTAMASH MOBILE') }}</h2>
                <p class="text-muted mb-0">New narsala road, opposite Dhanashree apartment</p>
                <p class="text-muted mb-0">Narsala, Nagpur, Maharashtra, 440034.</p>
                <p class="text-muted">Phone: 9226087986</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h1 class="fw-bold text-uppercase text-muted">Invoice</h1>
                <h5 class="mb-1"><strong>Invoice #:</strong> {{ $sale->invoice_number }}</h5>
                <p class="mb-0"><strong>Date:</strong> {{ $sale->sale_date->format('d M, Y') }}</p>
                <p class="mb-0"><strong>Status:</strong> 
                    @if($sale->payment_status == 'paid')
                        <span class="badge bg-success fs-6">PAID IN FULL</span>
                    @elseif($sale->payment_status == 'partial')
                        <span class="badge bg-warning text-dark fs-6">PARTIAL PAID</span>
                    @else
                        <span class="badge bg-danger fs-6">UNPAID</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded border">
                    <h5 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-user text-primary me-2"></i>Billed To:</h5>
                    @if($sale->customer)
                        <h6 class="fw-bold mb-1">
                            <a href="{{ route('admin.customers.show', $sale->customer) }}" class="text-decoration-none">
                                {{ $sale->customer->name }}
                            </a>
                        </h6>
                        <p class="mb-0 text-muted">Phone: {{ $sale->customer->phone ?? 'N/A' }}</p>
                        @if($sale->customer->email)
                            <p class="mb-0 text-muted">Email: {{ $sale->customer->email }}</p>
                        @endif
                        @if($sale->customer->address)
                            <p class="mb-0 text-muted">Address: {{ $sale->customer->address }}</p>
                        @endif
                    @else
                        <h6 class="fw-bold mb-1 text-muted">Walk-in Customer</h6>
                        <p class="mb-0 text-muted">Standard POS counter sale</p>
                    @endif
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold mb-1 text-dark">Payment Details</h6>
                    <p class="mb-1 text-muted">Initial Payment Method: <strong class="text-dark">{{ $sale->payment_method ?? 'Cash' }}</strong></p>
                    <p class="mb-0 text-muted">Sold By: <strong class="text-dark">{{ $sale->creator->name ?? 'Admin' }}</strong></p>
                </div>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="fw-bold">{{ $item->product->name ?? 'Product Deleted' }}</span>
                            @if(isset($item->product->sku))
                                <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                            @endif
                            @if(isset($item->product->barcode))
                                <small class="text-muted ms-2">Barcode: {{ $item->product->barcode }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="p-3 bg-light rounded border mb-3">
                    <h6 class="fw-bold text-dark mb-1"><i class="fa-solid fa-clipboard-list me-1"></i> Payment History &amp; Notes:</h6>
                    <p class="text-muted mb-0 small" style="white-space: pre-line;">{{ $sale->notes ?? 'Thank you for shopping with us!' }}</p>
                </div>
            </div>
            <div class="col-md-5">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-end"><strong>Subtotal:</strong></td>
                        <td class="text-end">₹{{ number_format($sale->subtotal, 2) }}</td>
                    </tr>
                    @if($sale->discount > 0)
                    <tr>
                        <td class="text-end"><strong>Discount:</strong></td>
                        <td class="text-end text-danger">- ₹{{ number_format($sale->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($sale->tax > 0)
                    <tr>
                        <td class="text-end"><strong>Tax:</strong></td>
                        <td class="text-end">₹{{ number_format($sale->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top border-bottom">
                        <td class="text-end py-3"><h4 class="fw-bold mb-0">Grand Total:</h4></td>
                        <td class="text-end py-3"><h4 class="fw-bold text-primary mb-0">₹{{ number_format($sale->grand_total, 2) }}</h4></td>
                    </tr>
                    <tr>
                        <td class="text-end pt-3 fw-semibold">Amount Paid:</td>
                        <td class="text-end pt-3 text-success fw-bold">₹{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end fw-semibold">Pending Due:</td>
                        <td class="text-end fw-bold {{ $sale->due_amount > 0 ? 'text-danger fs-5' : 'text-muted' }}">
                            ₹{{ number_format($sale->due_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="mt-5 text-center text-muted border-top pt-3">
            <small>This is a computer-generated invoice and does not require a signature.</small>
        </div>
    </div>
</div>

<!-- Modal: Receive Due Payment -->
@if($sale->due_amount > 0)
<div class="modal fade" id="payDueModal" tabindex="-1" aria-labelledby="payDueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.sales.pay-due', $sale) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="payDueModalLabel">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i>Receive Due Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3 text-center">
                        <span class="text-muted small d-block">Current Outstanding Due</span>
                        <span class="fs-3 fw-bold text-danger">₹{{ number_format($sale->due_amount, 2) }}</span>
                        <small class="text-muted d-block mt-1">Invoice: <strong>{{ $sale->invoice_number }}</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Amount Received (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="amount" 
                                   class="form-control form-control-lg fw-bold number-only" 
                                   value="{{ $sale->due_amount }}" 
                                   max="{{ $sale->due_amount }}"
                                   required>
                        </div>
                        <small class="text-muted">Enter full or partial payment amount</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select form-select-lg" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI / GPay / PhonePe</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Payment Note (Optional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Paid in cash at counter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-check-circle me-1"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
