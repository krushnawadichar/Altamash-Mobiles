@extends('layouts.admin')

@section('title', 'Technician - ' . $technician->name)
@section('page_title', 'Technician Profile & Repairs')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $technician->name }}</h4>
            <small class="text-muted"><i class="bi bi-telephone me-1"></i>+91 {{ $technician->mobile }} &bull; {{ $technician->specialization ?: 'General Technician' }}</small>
        </div>
        <a href="{{ route('admin.technicians.index') }}" class="btn btn-light border btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Assigned Repairs</div>
                <h4 class="fw-bold mb-0 mt-1">{{ $technician->repairJobs->count() }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Completed & Delivered</div>
                <h4 class="fw-bold mb-0 mt-1 text-success">{{ $technician->repairJobs->where('status', 'Delivered')->count() }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Commission Rate</div>
                <h4 class="fw-bold mb-0 mt-1 text-primary">{{ $technician->commission_rate }}%</h4>
            </div>
        </div>
    </div>

    <!-- Assigned Repair Jobs -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <span class="fw-bold"><i class="bi bi-tools me-1 text-primary"></i>Assigned Repair Jobs ({{ $technician->repairJobs->count() }})</span>
        </div>
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Job No</th>
                        <th>Customer</th>
                        <th>Device Model</th>
                        <th>Problem Complaint</th>
                        <th>Final Cost</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($technician->repairJobs as $job)
                    <tr>
                        <td>
                            <a href="{{ route('admin.repairs.show', $job) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $job->repair_no }}
                            </a>
                        </td>
                        <td>{{ $job->customer_name }}</td>
                        <td>{{ $job->model_name }}</td>
                        <td><small class="text-secondary">{{ Str::limit($job->problem_complaint, 35) }}</small></td>
                        <td class="fw-bold">₹{{ number_format($job->final_cost, 2) }}</td>
                        <td><span class="badge {{ $job->status_badge_class }}">{{ $job->status }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.repairs.show', $job) }}" class="btn btn-sm btn-light border py-0 px-2"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">No jobs assigned to this technician.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
