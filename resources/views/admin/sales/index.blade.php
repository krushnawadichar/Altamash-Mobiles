@extends('layouts.admin')

@section('title', 'Sales Invoices')
@section('page_title', 'Sales & Invoices')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Sales Invoices</h4>
            <small class="text-muted">Manage POS transactions, printable receipts, and customer dues</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sales.returns.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Sales Returns
            </a>
            <a href="{{ route('admin.pos.index') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-calculator-fill me-1"></i> Open POS Terminal
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.sales.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search invoice no, customer name, mobile, IMEI..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Payment Statuses</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="due" {{ request('payment_status') == 'due' ? 'selected' : '' }}>Due</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Sale Date</th>
                        <th>Grand Total</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th>Payment Status</th>
                        <th>Method</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>
                            <a href="{{ route('admin.sales.show', $sale) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $sale->invoice_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $sale->customer_name ?: 'Walk-in Customer' }}</div>
                            <small class="text-muted">{{ $sale->customer_mobile ?: '' }}</small>
                        </td>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        <td class="fw-bold text-dark">₹{{ number_format($sale->grand_total, 2) }}</td>
                        <td class="text-success fw-semibold">₹{{ number_format($sale->paid_amount, 2) }}</td>
                        <td>
                            <span class="{{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                ₹{{ number_format($sale->due_amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $sale->payment_badge_class }}">
                                {{ ucfirst($sale->payment_status) }}
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $sale->payment_method }}</span></td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border py-0 px-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item" href="{{ route('admin.sales.show', $sale) }}"><i class="bi bi-eye me-2"></i>View Sale</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.pos.invoice', $sale) }}"><i class="bi bi-printer me-2"></i>Print Invoice</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.pos.pdf', $sale) }}"><i class="bi bi-file-earmark-pdf me-2"></i>Download PDF</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @if($sale->status !== 'cancelled')
                                    <li>
                                        <form action="{{ route('admin.sales.cancel', $sale) }}" method="POST" onsubmit="return confirm('Cancel this sale? Stock and IMEIs will automatically be restored.');">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-x-circle me-2"></i>Cancel Sale
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No sales invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $sales->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
