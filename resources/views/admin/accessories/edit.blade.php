@extends('layouts.app')

@section('title', 'Edit Accessory - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-edit me-2 text-warning"></i> Edit Accessory
        </h2>
        <a href="{{ route('admin.accessories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.accessories.update', $accessory->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $accessory->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="Cover" {{ old('type', $accessory->type) == 'Cover' ? 'selected' : '' }}>Cover</option>
                                <option value="Tempered Glass" {{ old('type', $accessory->type) == 'Tempered Glass' ? 'selected' : '' }}>Tempered Glass</option>
                                <option value="Charger" {{ old('type', $accessory->type) == 'Charger' ? 'selected' : '' }}>Charger</option>
                                <option value="Earphones" {{ old('type', $accessory->type) == 'Earphones' ? 'selected' : '' }}>Earphones</option>
                                <option value="Cable" {{ old('type', $accessory->type) == 'Cable' ? 'selected' : '' }}>Cable</option>
                                <option value="Power Bank" {{ old('type', $accessory->type) == 'Power Bank' ? 'selected' : '' }}>Power Bank</option>
                                <option value="Bluetooth" {{ old('type', $accessory->type) == 'Bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                                <option value="Memory Card" {{ old('type', $accessory->type) == 'Memory Card' ? 'selected' : '' }}>Memory Card</option>
                                <option value="SIM Adapter" {{ old('type', $accessory->type) == 'SIM Adapter' ? 'selected' : '' }}>SIM Adapter</option>
                            </select>
                            @error('type')
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
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $accessory->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="purchase_price" class="form-label fw-bold">Purchase Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" 
                                   id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $accessory->purchase_price) }}" required>
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="selling_price" class="form-label fw-bold">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror" 
                                   id="selling_price" name="selling_price" value="{{ old('selling_price', $accessory->selling_price) }}" required>
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gst_percentage" class="form-label fw-bold">GST %</label>
                            <input type="number" step="0.01" class="form-control @error('gst_percentage') is-invalid @enderror" 
                                   id="gst_percentage" name="gst_percentage" value="{{ old('gst_percentage', $accessory->gst_percentage) }}">
                            @error('gst_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="minimum_stock" class="form-label fw-bold">Minimum Stock Alert</label>
                            <input type="number" class="form-control @error('minimum_stock') is-invalid @enderror" 
                                   id="minimum_stock" name="minimum_stock" value="{{ old('minimum_stock', $accessory->minimum_stock) }}">
                            @error('minimum_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $accessory->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Image</label>
                            @if($accessory->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($accessory->image) }}" 
                                         alt="{{ $accessory->name }}" 
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
                                       {{ $accessory->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.accessories.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i> Update Accessory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection