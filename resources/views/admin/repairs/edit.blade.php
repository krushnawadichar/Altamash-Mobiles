@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Edit Repair: {{ $repair->repair_code }}</h2>
        <a href="{{ route('admin.repairs.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Repairs</a>
    </div>
</div>

<form action="{{ route('admin.repairs.update', $repair) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Customer & Device Info -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer & Device Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $repair->customer_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Phone <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', $repair->customer_phone) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device Name / Model <span class="text-danger">*</span></label>
                        <input type="text" name="device_name" class="form-control" value="{{ old('device_name', $repair->device_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Problem Description <span class="text-danger">*</span></label>
                        <textarea name="problem_description" class="form-control" rows="3" required>{{ old('problem_description', $repair->problem_description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Technician Notes (Internal)</label>
                        <textarea name="technician_notes" class="form-control bg-light" rows="3">{{ old('technician_notes', $repair->technician_notes) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Costs & Status -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Tracking & Cost</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Received Date <span class="text-danger">*</span></label>
                        <input type="date" name="received_date" class="form-control" value="{{ old('received_date', $repair->received_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery" class="form-control" value="{{ old('expected_delivery', $repair->expected_delivery ? $repair->expected_delivery->format('Y-m-d') : '') }}">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Estimated Cost (₹)</label>
                        <input type="number" step="0.01" name="estimated_cost" class="form-control" value="{{ old('estimated_cost', $repair->estimated_cost) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Final Cost (₹)</label>
                        <input type="number" step="0.01" name="final_cost" class="form-control border-success text-success fw-bold" value="{{ old('final_cost', $repair->final_cost) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Advance Payment (₹)</label>
                        <input type="number" step="0.01" name="advance_payment" class="form-control" value="{{ old('advance_payment', $repair->advance_payment) }}">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select form-select-lg">
                            <option value="Received" {{ $repair->status == 'Received' ? 'selected' : '' }}>Received</option>
                            <option value="In Progress" {{ $repair->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Waiting for Parts" {{ $repair->status == 'Waiting for Parts' ? 'selected' : '' }}>Waiting for Parts</option>
                            <option value="Ready" {{ $repair->status == 'Ready' ? 'selected' : '' }}>Ready (Fixed)</option>
                            <option value="Delivered" {{ $repair->status == 'Delivered' ? 'selected' : '' }}>Delivered to Customer</option>
                            <option value="Cancelled" {{ $repair->status == 'Cancelled' ? 'selected' : '' }}>Cancelled / Cannot Fix</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold">Update Repair Job</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection


