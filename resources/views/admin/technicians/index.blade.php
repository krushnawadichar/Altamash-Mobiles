@extends('layouts.admin')

@section('title', 'Technician Management')
@section('page_title', 'Repair Technicians')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Technicians</h4>
            <small class="text-muted">Manage workshop repair technicians and service engineers</small>
        </div>
        <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createTechModal">
            <i class="bi bi-person-plus me-1"></i> Add Technician
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Technician Name</th>
                        <th>Contact Number</th>
                        <th>Specialization</th>
                        <th>Commission (%)</th>
                        <th>Assigned Repairs</th>
                        <th>Completed Repairs</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($technicians as $tech)
                    <tr>
                        <td class="fw-bold text-dark">
                            <a href="{{ route('admin.technicians.show', $tech) }}" class="text-decoration-none text-dark">
                                {{ $tech->name }}
                            </a>
                        </td>
                        <td>
                            <a href="tel:{{ $tech->mobile }}" class="text-decoration-none text-secondary">
                                <i class="bi bi-telephone me-1"></i>{{ $tech->mobile }}
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $tech->specialization ?: 'Hardware & Software' }}</span></td>
                        <td>{{ $tech->commission_rate }}%</td>
                        <td><span class="badge bg-primary-subtle text-primary">{{ $tech->repair_jobs_count }} Jobs</span></td>
                        <td>
                            <span class="badge bg-success-subtle text-success">
                                {{ $tech->repairJobs()->where('status', 'Delivered')->count() }} Done
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $tech->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($tech->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.technicians.show', $tech) }}" class="btn btn-sm btn-light border py-0 px-2">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No technicians registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Create Technician -->
<div class="modal fade" id="createTechModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.technicians.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Service Technician</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Technician name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mobile Phone <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control" placeholder="10-digit mobile" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Specialization</label>
                        <input type="text" name="specialization" class="form-control" placeholder="e.g. Display & Glass, iPhone Chip Level, Charging IC">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Commission Rate (%)</label>
                        <input type="number" step="0.1" name="commission_rate" class="form-control" value="0.0">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Technician</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
