@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Add Repair Job</h2>
        <a href="{{ route('admin.repairs.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Repairs</a>
    </div>
</div>

<form action="{{ route('admin.repairs.store') }}" method="POST">
    @csrf
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
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Customer Phone <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" required>
                            @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device Name / Model <span class="text-danger">*</span></label>
                        <input type="text" name="device_name" class="form-control @error('device_name') is-invalid @enderror" value="{{ old('device_name') }}" required placeholder="e.g. Samsung Galaxy S21 / LG 32inch TV">
                        @error('device_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Problem Description <span class="text-danger">*</span></label>
                        <textarea name="problem_description" class="form-control @error('problem_description') is-invalid @enderror" rows="4" required>{{ old('problem_description') }}</textarea>
                        @error('problem_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery" class="form-control" value="{{ old('expected_delivery') }}">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Estimated Cost (₹)</label>
                        <input type="number" step="0.01" name="estimated_cost" class="form-control" value="{{ old('estimated_cost', 0) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Advance Payment (₹)</label>
                        <input type="number" step="0.01" name="advance_payment" class="form-control" value="{{ old('advance_payment', 0) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Received">Received</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Waiting for Parts">Waiting for Parts</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Save Repair Job</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection


