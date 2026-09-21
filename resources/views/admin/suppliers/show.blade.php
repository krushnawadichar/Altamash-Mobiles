@extends('layouts.admin')

@section('title', 'Supplier Profile - ' . $supplier->name)
@section('page_title', 'Supplier Ledger & Orders')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $supplier->name }}</h4>
            <small class="text-muted">{{ $supplier->company_name ?: 'Distributor' }} &bull; GSTIN: {{ $supplier->gst_number ?: '-' }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-cart-plus me-1"></i> New Purchase Order
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Purchases</div>
                <h4 class="fw-bold mb-0 mt-1">₹{{ number_format($supplier->total_purchases, 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Paid Amount</div>
                <h4 class="fw-bold mb-0 mt-1 text-success">₹{{ number_format($supplier->total_paid, 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Outstanding Balance (Payable)</div>
                <h4 class="fw-bold mb-0 mt-1 text-danger">₹{{ number_format($supplier->current_balance, 2) }}</h4>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Purchase Orders History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-bag-check me-1 text-primary"></i>Purchase Orders ({{ $supplier->purchases->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Purchase No</th>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplier->purchases as $p)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.purchases.show', $p) }}" class="fw-bold text-decoration-none">
                                        {{ $p->purchase_no }}
                                    </a>
                                </td>
                                <td>{{ $p->purchase_date->format('d M Y') }}</td>
                                <td>{{ $p->supplier_invoice_no ?: '-' }}</td>
                                <td class="fw-bold">₹{{ number_format($p->grand_total, 2) }}</td>
                                <td class="text-success">₹{{ number_format($p->paid_amount, 2) }}</td>
                                <td class="{{ $p->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    ₹{{ number_format($p->due_amount, 2) }}
                                </td>
                                <td><span class="badge {{ $p->payment_badge_class }}">{{ ucfirst($p->payment_status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No purchases recorded from this supplier.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supplier Information & Payments -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-person-lines-fill me-1 text-secondary"></i>Contact Information</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2"><small class="text-muted d-block">Phone Number</small> <strong>{{ $supplier->mobile }}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Email</small> <strong>{{ $supplier->email ?: '-' }}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Address</small> <span>{{ $supplier->address ?: '-' }}</span></div>
                    <div class="mb-0"><small class="text-muted d-block">GSTIN</small> <code>{{ $supplier->gst_number ?: '-' }}</code></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-wallet2 me-1 text-success"></i>Payment History</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplier->payments as $pmt)
                            <tr>
                                <td>{{ $pmt->payment_date->format('d M Y') }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($pmt->amount, 2) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ strtoupper($pmt->payment_method) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-2">No payments logged yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
