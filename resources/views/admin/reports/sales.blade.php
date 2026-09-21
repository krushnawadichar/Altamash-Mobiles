@extends('layouts.admin')

@section('title', 'Sales Analytics & Report')
@section('page_title', 'Sales Reports')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Sales Analytics Report</h4>
            <small class="text-muted">Performance analysis across revenue, payment modes, and product margins</small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm no-print">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Date Filters -->
    <div class="card border-0 shadow-sm mb-3 no-print">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.sales') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Customer</label>
                    <select name="customer_id" class="form-select form-select-sm">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Apply Filter</button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Sales Revenue</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">₹{{ number_format($totalRevenue, 2) }}</h3>
                <small class="text-muted">{{ $totalInvoices }} invoices</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Paid (Cash/UPI/Card)</div>
                <h3 class="fw-bold text-success mb-0 mt-1">₹{{ number_format($totalPaid, 2) }}</h3>
                <small class="text-muted">Collected in tender</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Customer Outstanding Due</div>
                <h3 class="fw-bold text-danger mb-0 mt-1">₹{{ number_format($totalDue, 2) }}</h3>
                <small class="text-muted">Receivables pending</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Estimated Gross Profit</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">₹{{ number_format($totalProfit, 2) }}</h3>
                <small class="text-muted">Revenue minus COGS</small>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <span class="fw-bold"><i class="bi bi-table me-1 text-primary"></i>Sales Invoices Log</span>
        </div>
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Grand Total</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th>Profit Margin</th>
                        <th>Method</th>
                        <th class="text-end no-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td class="fw-bold font-monospace">{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        <td>{{ $sale->customer_name ?: 'Walk-in Customer' }}</td>
                        <td class="fw-bold">₹{{ number_format($sale->grand_total, 2) }}</td>
                        <td class="text-success">₹{{ number_format($sale->paid_amount, 2) }}</td>
                        <td class="{{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            ₹{{ number_format($sale->due_amount, 2) }}
                        </td>
                        <td class="text-success fw-semibold">₹{{ number_format($sale->profit, 2) }}</td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $sale->payment_method }}</span></td>
                        <td class="text-end no-print">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-light border py-0 px-2"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No sales found within the selected date range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="card-footer bg-white py-2 no-print">
            {{ $sales->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
