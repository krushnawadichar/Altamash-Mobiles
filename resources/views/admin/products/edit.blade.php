@extends('layouts.app')

@section('title', 'Edit Product - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-edit me-2 text-warning"></i> Edit Product
        </h2>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="brand_id" class="form-label fw-bold">Brand <span class="text-danger">*</span></label>
                            <select class="form-select @error('brand_id') is-invalid @enderror" 
                                    id="brand_id" name="brand_id" required>
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label fw-bold">Supplier</label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                    id="supplier_id" name="supplier_id">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="unit_id" class="form-label fw-bold">Unit <span class="text-danger">*</span></label>
                            <select class="form-select @error('unit_id') is-invalid @enderror" 
                                    id="unit_id" name="unit_id" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="product_type_id" class="form-label fw-bold">Product Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('product_type_id') is-invalid @enderror" 
                                    id="product_type_id" name="product_type_id" required>
                                <option value="">Select Type</option>
                                @foreach($productTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('product_type_id', $product->product_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="mobile_company_id" class="form-label fw-bold">Mobile Company</label>
                            <select class="form-select @error('mobile_company_id') is-invalid @enderror" 
                                    id="mobile_company_id" name="mobile_company_id">
                                <option value="">Select Company</option>
                                @foreach($mobileCompanies as $company)
                                    <option value="{{ $company->id }}" {{ old('mobile_company_id', $product->mobile_company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('mobile_company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="purchase_price" class="form-label fw-bold">Purchase Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" 
                                   id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="selling_price" class="form-label fw-bold">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" 
                                   id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gst_percentage" class="form-label fw-bold">GST %</label>
                            <input type="number" step="0.01" class="form-control @error('gst_percentage') is-invalid @enderror" 
                                   id="gst_percentage" name="gst_percentage" value="{{ old('gst_percentage', $product->gst_percentage) }}">
                            @error('gst_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="color" class="form-label fw-bold">Color</label>
                            <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                   id="color" name="color" value="{{ old('color', $product->color) }}">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="storage" class="form-label fw-bold">Storage</label>
                            <input type="text" class="form-control @error('storage') is-invalid @enderror" 
                                   id="storage" name="storage" value="{{ old('storage', $product->storage) }}">
                            @error('storage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ram" class="form-label fw-bold">RAM</label>
                            <input type="text" class="form-control @error('ram') is-invalid @enderror" 
                                   id="ram" name="ram" value="{{ old('ram', $product->ram) }}">
                            @error('ram')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            <div class="col-md-4">
                        <div class="mb-3">
                            <label for="current_stock" class="form-label fw-bold">Current Stock</label>
                            <input type="number" class="form-control @error('current_stock') is-invalid @enderror" 
                                   id="current_stock" name="current_stock" value="{{ old('current_stock', $product->current_stock) }}">
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="minimum_stock" class="form-label fw-bold">Minimum Stock Alert</label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                   id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock) }}">
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Product Image</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="max-width: 100px; max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1" 
                                       {{ $product->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection