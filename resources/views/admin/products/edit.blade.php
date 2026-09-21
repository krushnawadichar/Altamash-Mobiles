@extends('layouts.admin')

@section('title', 'Edit Product - ' . $product->name)
@section('page_title', 'Edit Product')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Edit Product: {{ $product->name }}</h4>
            <small class="text-muted">Update product specifications, prices, or status</small>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Products
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">

            <!-- Left Col -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Basic Information</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Product Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="mobile" {{ old('type', $product->type) == 'mobile' ? 'selected' : '' }}>Mobile Phone (Track IMEI)</option>
                                    <option value="accessory" {{ old('type', $product->type) == 'accessory' ? 'selected' : '' }}>Mobile Accessory</option>
                                    <option value="spare_part" {{ old('type', $product->type) == 'spare_part' ? 'selected' : '' }}>Spare Part / Component</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">SKU / Product Code <span class="text-danger">*</span></label>
                                <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Brand</label>
                                <select name="brand_id" class="form-select select2">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Category</label>
                                <select name="category_id" class="form-select select2">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Model Number</label>
                                <input type="text" name="model_no" class="form-control" value="{{ old('model_no', $product->model_no) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Barcode / EAN</label>
                                <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-currency-rupee me-1 text-success"></i>Pricing & Tax</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Purchase Cost (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Retail Selling Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-control fw-bold text-success" value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Wholesale Price (₹)</label>
                            <input type="number" step="0.01" name="wholesale_price" class="form-control" value="{{ old('wholesale_price', $product->wholesale_price) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" value="{{ old('mrp', $product->mrp) }}">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Tax / GST (%)</label>
                            <input type="number" step="0.01" name="tax_percent" class="form-control" value="{{ old('tax_percent', $product->tax_percent) }}">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-gear me-1 text-secondary"></i>Controls & Status</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Minimum Stock Alert Level <span class="text-danger">*</span></label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold small">Change Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="rounded border" width="60">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Update Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light border">Cancel</a>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
