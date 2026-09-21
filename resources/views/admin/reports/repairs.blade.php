@extends('layouts.admin')

@section('title', 'Repair Service Analytics & Report')
@section('page_title', 'Repair Service Reports')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Repair Service Reports</h4>
            <small class="text-muted">Servicing volume, repair billings, spare parts consumption, and technician outputs</small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm no-print">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Date Filters -->
    <div class="card border-0 shadow-sm mb-3 no-print">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.repairs') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Technician</label>
                    <select name="technician_id" class="form-select form-select-sm">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $t)
                            <option value="{{ $t->id }}" {{ request('technician_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Apply Filter</button>
                    <a href="{{ route('admin.reports.repairs') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Repair Jobs</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalJobs }}</h3>
                <small class="text-muted">Devices received for service</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Delivered / Completed</div>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ $completedJobs }}</h3>
                <small class="text-muted">Successful repairs</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Repair Revenue</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">₹{{ number_format($totalRevenue, 2) }}</h3>
                <small class="text-muted">Parts + Labor billed</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Spare Parts Consumed</div>
                <h3 class="fw-bold text-secondary mb-0 mt-1">₹{{ number_format($partsCost, 2) }}</h3>
                <small class="text-muted">Billed spare part components</small>
            </div>
        </div>
    </div>

    <!-- Repairs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <span class="fw-bold"><i class="bi bi-tools me-1 text-primary"></i>Service Jobs Log</span>
        </div>
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Job No</th>
                        <th>Received Date</th>
                        <th>Customer</th>
                        <th>Device Model</th>
                        <th>Technician</th>
                        <th>Final Cost</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairs as $r)
                    <tr>
                        <td>
                            <a href="{{ route('admin.repairs.show', $r) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $r->repair_no }}
                            </a>
                        </td>
                        <td>{{ $r->received_date->format('d M Y') }}</td>
                        <td>{{ $r->customer_name }}</td>
                        <td>{{ $r->model_name }}</td>
                        <td>{{ $r->technician?->name ?? 'Unassigned' }}</td>
                        <td class="fw-bold">₹{{ number_format($r->final_cost, 2) }}</td>
                        <td class="text-success">₹{{ number_format($r->paid_amount, 2) }}</td>
                        <td class="{{ $r->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            ₹{{ number_format($r->due_amount, 2) }}
                        </td>
                        <td><span class="badge {{ $r->status_badge_class }}">{{ $r->status }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No repair records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($repairs->hasPages())
        <div class="card-footer bg-white py-2 no-print">
            {{ $repairs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
