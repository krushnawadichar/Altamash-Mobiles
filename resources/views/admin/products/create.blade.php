@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Add Product</h2>
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Products</a>
    </div>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <!-- Main Details -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model Number</label>
                            <input type="text" name="model_number" class="form-control" value="{{ old('model_number') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Pricing & Inventory</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price</label>
                            <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ old('purchase_price', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', 0) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">MRP</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" value="{{ old('mrp') }}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">GST / Tax (%)</label>
                            <input type="number" step="0.01" name="tax" class="form-control" value="{{ old('tax', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Initial Stock Qty</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Stock Alert</label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', 5) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Images</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Main Image</label>
                        <input type="file" name="main_image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gallery Images (Multiple)</label>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Organization</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Product Options</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="status" value="1" class="form-check-input" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="featured" value="1" class="form-check-input">
                        <label class="form-check-label">Featured Product</label>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="new_arrival" value="1" class="form-check-input">
                        <label class="form-check-label">New Arrival</label>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="best_seller" value="1" class="form-check-input">
                        <label class="form-check-label">Best Seller</label>
                    </div>
                    <hr>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="requires_serial" value="1" class="form-check-input">
                        <label class="form-check-label text-primary fw-bold"><i class="fa-solid fa-barcode"></i> Requires IMEI/Serial Tracking</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Save Product</button>
        </div>
    </div>
</form>
@endsection


