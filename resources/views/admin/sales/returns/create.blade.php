@extends('layouts.admin')

@section('title', 'Process Sales Return')
@section('page_title', 'New Sales Return')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Process Customer Return</h4>
            <small class="text-muted">Lookup invoice, select items to return, and restock inventory</small>
        </div>
        <a href="{{ route('admin.sales.returns.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Returns
        </a>
    </div>

    <!-- 1. Invoice Lookup Form -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.sales.returns.create') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Enter Sales Invoice Number</label>
                    <div class="input-group">
                        <input type="text" name="invoice_no" class="form-control" placeholder="e.g. INV-2026-000001" value="{{ request('invoice_no') }}" required>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bi bi-search me-1"></i> Find Invoice
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($sale)
    <!-- 2. Return Items Processing Form -->
    <form action="{{ route('admin.sales.returns.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-receipt me-1 text-primary"></i>Invoice: {{ $sale->invoice_no }} (Customer: {{ $sale->customer_name ?: 'Walk-in' }})</span>
                <span class="badge bg-light text-dark border">Date: {{ $sale->sale_date->format('d M Y') }}</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive mb-3">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">Return?</th>
                                <th>Product Details</th>
                                <th>Sold Qty</th>
                                <th>Unit Price</th>
                                <th style="width: 120px;">Return Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $idx => $item)
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" name="items[{{ $idx }}][selected]" value="1" id="check_{{ $idx }}" checked>
                                    <input type="hidden" name="items[{{ $idx }}][sale_item_id]" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <label class="form-check-label fw-bold text-dark d-block" for="check_{{ $idx }}">
                                        {{ $item->product->name }}
                                    </label>
                                    <div class="text-muted small">
                                        SKU: {{ $item->product->sku }}
                                        @if($item->imei)
                                            &bull; <strong class="text-primary font-monospace">IMEI: {{ $item->imei }}</strong>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                <td>
                                    <input type="number" name="items[{{ $idx }}][quantity]" class="form-control form-control-sm" value="1" min="1" max="{{ $item->quantity }}" {{ $item->product->isMobile() ? 'readonly' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Refund Tender Method</label>
                        <select name="refund_method" class="form-select">
                            <option value="cash" selected>Cash Refund</option>
                            <option value="upi">UPI / Bank Transfer</option>
                            <option value="credit_note">Store Credit / Customer Balance</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Reason for Return</label>
                        <input type="text" name="reason" class="form-control" placeholder="e.g. Defective piece, wrong model, customer changed mind">
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-danger fw-bold px-4">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Confirm & Restock Return
                </button>
            </div>
        </div>
    </form>
    @elseif(request('invoice_no'))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> No sale invoice found matching <strong>{{ request('invoice_no') }}</strong>. Please verify the invoice number.
        </div>
    @endif

</div>
@endsection
