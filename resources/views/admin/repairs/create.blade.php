@extends('layouts.app')

@section('title', 'Create Repair - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Create Repair
        </h2>
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.repairs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-bold">Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" name="customer_id">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} - {{ $customer->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-bold">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                   id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_mobile" class="form-label fw-bold">Customer Mobile <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('customer_mobile') is-invalid @enderror" 
                                   id="customer_mobile" name="customer_mobile" value="{{ old('customer_mobile') }}" required>
                            @error('customer_mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="device_name" class="form-label fw-bold">Device Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('device_name') is-invalid @enderror" 
                                   id="device_name" name="device_name" value="{{ old('device_name') }}" required>
                            @error('device_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="imei" class="form-label fw-bold">IMEI</label>
                            <input type="text" class="form-control @error('imei') is-invalid @enderror" 
                                   id="imei" name="imei" value="{{ old('imei') }}">
                            @error('imei')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="repair_status_id" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('repair_status_id') is-invalid @enderror" 
                                    id="repair_status_id" name="repair_status_id" required>
                                <option value="">Select Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('repair_status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('repair_status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="issue" class="form-label fw-bold">Issue <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('issue') is-invalid @enderror" 
                                      id="issue" name="issue" rows="3" required>{{ old('issue') }}</textarea>
                            @error('issue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="accessories_received" class="form-label fw-bold">Accessories Received</label>
                            <textarea class="form-control @error('accessories_received') is-invalid @enderror" 
                                      id="accessories_received" name="accessories_received" rows="2">{{ old('accessories_received') }}</textarea>
                            @error('accessories_received')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="estimated_cost" class="form-label fw-bold">Estimated Cost</label>
                            <input type="number" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                   id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost', 0) }}">
                            @error('estimated_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="advance_paid" class="form-label fw-bold">Advance Paid</label>
                            <input type="number" step="0.01" class="form-control @error('advance_paid') is-invalid @enderror" 
                                   id="advance_paid" name="advance_paid" value="{{ old('advance_paid', 0) }}">
                            @error('advance_paid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="receive_date" class="form-label fw-bold">Receive Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('receive_date') is-invalid @enderror" 
                                   id="receive_date" name="receive_date" value="{{ old('receive_date', date('Y-m-d')) }}" required>
                            @error('receive_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="engineer_notes" class="form-label fw-bold">Engineer Notes</label>
                            <textarea class="form-control @error('engineer_notes') is-invalid @enderror" 
                                      id="engineer_notes" name="engineer_notes" rows="2">{{ old('engineer_notes') }}</textarea>
                            @error('engineer_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="images" class="form-label fw-bold">Images</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            <small class="text-muted">You can upload multiple images</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.repairs.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Repair
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection