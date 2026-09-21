@extends('layouts.admin')

@section('title', 'Create Repair Job Card')
@section('page_title', 'New Repair Intake')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Create Repair Job Card</h4>
            <small class="text-muted">Register customer device for diagnostic inspection and servicing</small>
        </div>
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Repairs
        </a>
    </div>

    <form action="{{ route('admin.repairs.store') }}" method="POST">
        @csrf
        <div class="row g-3">

            <!-- Left Col: Customer & Device Details -->
            <div class="col-lg-8">
                <!-- Customer Details -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-person me-1 text-primary"></i>Customer Information</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Customer full name" value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Customer Mobile Phone <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">+91</span>
                                    <input type="text" name="customer_mobile" class="form-control" placeholder="10-digit mobile number" value="{{ old('customer_mobile') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Device Information -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-phone me-1 text-primary"></i>Device Information</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Brand</label>
                                <select name="brand_id" class="form-select select2">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Phone / Device Model <span class="text-danger">*</span></label>
                                <input type="text" name="model_name" class="form-control" placeholder="e.g. Galaxy S22 Ultra or iPhone 14" value="{{ old('model_name') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Device Color</label>
                                <input type="text" name="color" class="form-control" placeholder="e.g. Midnight Black" value="{{ old('color') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Device IMEI Number</label>
                                <input type="text" name="imei" class="form-control font-monospace" placeholder="15-digit IMEI" value="{{ old('imei') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Serial Number</label>
                                <input type="text" name="serial_no" class="form-control font-monospace" placeholder="Serial / Board number" value="{{ old('serial_no') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Problem / Customer Complaint <span class="text-danger">*</span></label>
                                <textarea name="problem_complaint" class="form-control" rows="3" placeholder="Describe the defect, symptoms (e.g. Not charging, display cracked, water damaged, speaker buzzing...)" required>{{ old('problem_complaint') }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Physical Condition on Intake</label>
                                <input type="text" name="physical_condition" class="form-control" placeholder="e.g. Minor body scratches, cracked back, camera glass intact" value="{{ old('physical_condition') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Accessories Received with Device</label>
                                <input type="text" name="accessories_received" class="form-control" placeholder="e.g. Back cover, SIM tray, with charger" value="{{ old('accessories_received') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col: Technician, Cost & Advance -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-person-badge me-1 text-info"></i>Assignment & Timeline</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Assign Technician</label>
                            <select name="technician_id" class="form-select select2">
                                <option value="">Select Technician (Optional)</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ old('technician_id') == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }} ({{ $tech->specialization ?: 'General' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date', date('Y-m-d', strtotime('+2 days'))) }}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Service Warranty (Days)</label>
                            <input type="number" name="warranty_days" class="form-control" value="{{ old('warranty_days', 30) }}">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-currency-rupee me-1 text-success"></i>Estimated Cost & Advance</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Estimated Total Cost (₹)</label>
                            <input type="number" step="0.01" name="estimated_cost" class="form-control fw-bold" placeholder="0.00" value="{{ old('estimated_cost', 0) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Advance Payment Received (₹)</label>
                            <input type="number" step="0.01" name="advance_amount" class="form-control fw-bold text-success" placeholder="0.00" value="{{ old('advance_amount', 0) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Advance Payment Method</label>
                            <select name="payment_method" class="form-select form-select-sm">
                                <option value="cash" selected>Cash</option>
                                <option value="upi">UPI / QR Code</option>
                                <option value="card">Card</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Internal Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Private internal notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Generate Repair Job Card
                    </button>
                    <a href="{{ route('admin.repairs.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
