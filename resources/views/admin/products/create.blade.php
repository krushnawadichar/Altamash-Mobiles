@extends('layouts.admin')

@section('title', 'Add New Product')
@section('page_title', 'Create Product')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Add New Product</h4>
            <small class="text-muted">Register a smartphone unit, accessory, or repair part</small>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">

            <!-- Left Col: General Details -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Basic Information</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Apple iPhone 15 Pro 128GB or 20W Fast Charger" value="{{ old('name') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Product Type <span class="text-danger">*</span></label>
                                <select name="type" id="productTypeSelect" class="form-select" required>
                                    <option value="mobile" {{ old('type') == 'mobile' ? 'selected' : '' }}>Mobile Phone (Track IMEI)</option>
                                    <option value="accessory" {{ old('type') == 'accessory' ? 'selected' : '' }}>Mobile Accessory (Quantity)</option>
                                    <option value="spare_part" {{ old('type') == 'spare_part' ? 'selected' : '' }}>Spare Part / Component</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">SKU / Product Code <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control" value="{{ old('sku', $autoSku) }}" required>
                            </div>

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
                                <label class="form-label fw-semibold small">Category</label>
                                <select name="category_id" class="form-select select2">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Model Number</label>
                                <input type="text" name="model_no" class="form-control" placeholder="e.g. A3102 or SM-S928B" value="{{ old('model_no') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Barcode / EAN</label>
                                <div class="input-group">
                                    <input type="text" name="barcode" class="form-control" placeholder="Scan or enter barcode" value="{{ old('barcode') }}">
                                    <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Product Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Features, specifications, warranty notes...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IMEI Section for Mobile Phones -->
                <div class="card border-0 shadow-sm mb-3" id="mobileImeiSection">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-upc-scan me-1 text-primary"></i>Initial IMEI Registration (Optional)</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-2">
                            Enter existing IMEIs to add to initial stock (one IMEI per line). You can also add IMEIs anytime via <strong>Purchase Orders</strong>.
                        </p>
                        <textarea name="initial_imeis" class="form-control font-monospace" rows="4" placeholder="356789012345671&#10;356789012345672&#10;356789012345673">{{ old('initial_imeis') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Col: Pricing & Stock Limits -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-currency-rupee me-1 text-success"></i>Pricing & Tax</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Purchase Cost (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control" placeholder="0.00" value="{{ old('purchase_price') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Retail Selling Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-control fw-bold text-success" placeholder="0.00" value="{{ old('selling_price') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Wholesale Price (₹)</label>
                            <input type="number" step="0.01" name="wholesale_price" class="form-control" placeholder="0.00" value="{{ old('wholesale_price') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">MRP (Printed Price ₹)</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" placeholder="0.00" value="{{ old('mrp') }}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Tax / GST (%)</label>
                            <input type="number" step="0.01" name="tax_percent" class="form-control" value="{{ old('tax_percent', 18) }}">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-shield-exclamation me-1 text-warning"></i>Inventory Controls</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Minimum Stock Alert Level <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 5) }}" required>
                            <small class="text-muted">Trigger low-stock alert when quantity reaches this value.</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Save Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('productTypeSelect');
        const imeiSection = document.getElementById('mobileImeiSection');

        function toggleImei() {
            if (typeSelect.value === 'mobile') {
                imeiSection.style.display = 'block';
            } else {
                imeiSection.style.display = 'none';
            }
        }

        typeSelect.addEventListener('change', toggleImei);
        toggleImei();
    });
</script>
@endpush
