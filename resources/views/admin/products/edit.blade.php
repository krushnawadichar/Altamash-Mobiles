@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Edit Product: {{ $product->name }}</h2>
        <a href="{{ route('admin.products.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Products</a>
    </div>
</div>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
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
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $product->name) }}">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model Number</label>
                            <input type="text" name="model_number" class="form-control" value="{{ old('model_number', $product->model_number) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Description</label>
                        <textarea name="description" class="form-control" rows="5">{{ old('description', $product->description) }}</textarea>
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
                            <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ old('purchase_price', $product->purchase_price) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $product->selling_price) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">MRP</label>
                            <input type="number" step="0.01" name="mrp" class="form-control" value="{{ old('mrp', $product->mrp) }}">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">GST / Tax (%)</label>
                            <input type="number" step="0.01" name="tax" class="form-control" value="{{ old('tax', $product->tax) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Qty</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Min Stock Alert</label>
                            <input type="number" name="min_stock" class="form-control" value="{{ old('min_stock', $product->min_stock) }}">
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
                    @if($product->mainImage)
                        <div class="mb-3">
                            <label class="d-block fw-bold">Current Main Image</label>
                            <img src="{{ Storage::url($product->mainImage->image) }}" width="150" class="img-thumbnail">
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Update Main Image</label>
                        <input type="file" name="main_image" class="form-control" accept="image/*">
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Add Gallery Images</label>
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
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
                        <input type="checkbox" name="status" value="1" class="form-check-input" {{ old('status', $product->status) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="featured" value="1" class="form-check-input" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                        <label class="form-check-label">Featured Product</label>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="new_arrival" value="1" class="form-check-input" {{ old('new_arrival', $product->new_arrival) ? 'checked' : '' }}>
                        <label class="form-check-label">New Arrival</label>
                    </div>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="best_seller" value="1" class="form-check-input" {{ old('best_seller', $product->best_seller) ? 'checked' : '' }}>
                        <label class="form-check-label">Best Seller</label>
                    </div>
                    <hr>
                    <div class="mb-2 form-check">
                        <input type="checkbox" name="requires_serial" value="1" class="form-check-input" {{ old('requires_serial', $product->requires_serial) ? 'checked' : '' }}>
                        <label class="form-check-label text-primary fw-bold"><i class="fa-solid fa-barcode"></i> Requires IMEI/Serial Tracking</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fs-5">Update Product</button>
        </div>
    </div>
</form>
@endsection


