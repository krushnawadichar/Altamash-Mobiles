@extends('layouts.admin')

@section('title', 'Mobile Repair Management')
@section('page_title', 'Mobile Repair Jobs')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Mobile Repairing Management</h4>
            <small class="text-muted">Track device intake, diagnosis, parts usage, technician assignments, and deliveries</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.technicians.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-person-badge me-1"></i> Technicians
            </a>
            <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-tools me-1"></i> New Repair Job Card
            </a>
        </div>
    </div>

    <!-- Status Filter Buttons / Quick Filters -->
    <div class="d-flex gap-1 overflow-x-auto pb-2 mb-3">
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-light border' }} text-nowrap">
            All Repairs
        </a>
        <a href="{{ route('admin.repairs.index', ['status' => 'Received']) }}" class="btn btn-sm {{ request('status') == 'Received' ? 'btn-secondary' : 'btn-light border' }} text-nowrap">
            Received
        </a>
        <a href="{{ route('admin.repairs.index', ['status' => 'Under Diagnosis']) }}" class="btn btn-sm {{ request('status') == 'Under Diagnosis' ? 'btn-primary' : 'btn-light border' }} text-nowrap">
            Under Diagnosis
        </a>
        <a href="{{ route('admin.repairs.index', ['status' => 'Repairing']) }}" class="btn btn-sm {{ request('status') == 'Repairing' ? 'btn-primary' : 'btn-light border' }} text-nowrap">
            Repairing
        </a>
        <a href="{{ route('admin.repairs.index', ['status' => 'Waiting for Parts']) }}" class="btn btn-sm {{ request('status') == 'Waiting for Parts' ? 'btn-danger' : 'btn-light border' }} text-nowrap">
            Waiting for Parts
        </a>
        <a href="{{ route('admin.repairs.index', ['status' => 'Ready for Delivery']) }}" class="btn btn-sm {{ request('status') == 'Ready for Delivery' ? 'btn-success' : 'btn-light border' }} text-nowrap">
            Ready for Delivery
        </a>
        <a href="{{ route('admin.repairs.index', ['status' => 'Delivered']) }}" class="btn btn-sm {{ request('status') == 'Delivered' ? 'btn-dark' : 'btn-light border' }} text-nowrap">
            Delivered
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.repairs.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search Job No, Customer Mobile, IMEI, or Model..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses (12)</option>
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="technician_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.repairs.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Repairs Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Job No</th>
                        <th>Customer</th>
                        <th>Device Model</th>
                        <th>IMEI / Serial</th>
                        <th>Problem Complaint</th>
                        <th>Technician</th>
                        <th>Final Cost</th>
                        <th>Balance Due</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($repairs as $repair)
                    <tr>
                        <td>
                            <a href="{{ route('admin.repairs.show', $repair) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $repair->repair_no }}
                            </a>
                            <div class="text-muted" style="font-size: 0.72rem;">{{ $repair->received_date->format('d M, h:i A') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $repair->customer_name }}</div>
                            <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $repair->customer_mobile }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $repair->model_name }}</div>
                            <small class="text-muted">{{ $repair->brand?->name ?? '' }} {{ $repair->color ? '(' . $repair->color . ')' : '' }}</small>
                        </td>
                        <td>
                            @if($repair->imei)
                                <code class="text-dark bg-light px-1 border rounded">{{ $repair->imei }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-secondary d-inline-block text-truncate" style="max-width: 180px;" title="{{ $repair->problem_complaint }}">
                                {{ $repair->problem_complaint }}
                            </small>
                        </td>
                        <td>
                            @if($repair->technician)
                                <span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i>{{ $repair->technician->name }}</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis">Unassigned</span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">₹{{ number_format($repair->final_cost, 2) }}</td>
                        <td>
                            <span class="{{ $repair->due_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                ₹{{ number_format($repair->due_amount, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $repair->status_badge_class }}">
                                {{ $repair->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border py-0 px-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item" href="{{ route('admin.repairs.show', $repair) }}"><i class="bi bi-eye me-2"></i>Job Card Details</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.repairs.jobcard', $repair) }}" target="_blank"><i class="bi bi-printer me-2"></i>Print Job Card</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.repairs.invoice', $repair) }}" target="_blank"><i class="bi bi-receipt me-2"></i>Print Repair Bill</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.repairs.jobcard.pdf', $repair) }}"><i class="bi bi-file-earmark-pdf me-2"></i>Job Card PDF</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">No repair jobs found matching criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($repairs->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $repairs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
