@extends('layouts.admin')

@section('title', 'Purchases Analytics & Report')
@section('page_title', 'Purchases Reports')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Purchases Analytics Report</h4>
            <small class="text-muted">Procurement expenditure, supplier invoices, and accounts payable</small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm no-print">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Date Filters -->
    <div class="card border-0 shadow-sm mb-3 no-print">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.purchases') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Supplier</label>
                    <select name="supplier_id" class="form-select form-select-sm">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.reports.purchases') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Purchases Value</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">₹{{ number_format($totalPurchases, 2) }}</h3>
                <small class="text-muted">{{ $totalOrders }} purchase orders</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Amount Paid</div>
                <h3 class="fw-bold text-success mb-0 mt-1">₹{{ number_format($totalPaid, 2) }}</h3>
                <small class="text-muted">Settled payments</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Outstanding Supplier Due</div>
                <h3 class="fw-bold text-danger mb-0 mt-1">₹{{ number_format($totalDue, 2) }}</h3>
                <small class="text-muted">Accounts payable balance</small>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Purchase No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Invoice Ref</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                    <tr>
                        <td>
                            <a href="{{ route('admin.purchases.show', $p) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $p->purchase_no }}
                            </a>
                        </td>
                        <td>{{ $p->purchase_date->format('d M Y') }}</td>
                        <td>{{ $p->supplier->name }}</td>
                        <td><code>{{ $p->supplier_invoice_no ?: '-' }}</code></td>
                        <td class="fw-bold">₹{{ number_format($p->grand_total, 2) }}</td>
                        <td class="text-success">₹{{ number_format($p->paid_amount, 2) }}</td>
                        <td class="{{ $p->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            ₹{{ number_format($p->due_amount, 2) }}
                        </td>
                        <td><span class="badge {{ $p->payment_badge_class }}">{{ ucfirst($p->payment_status) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No purchase records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="card-footer bg-white py-2 no-print">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
