@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-box text-primary me-2"></i>Add Product</h2>
            <p class="text-muted mb-0">Fill in the details below to add a new product to your inventory</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please check the form errors:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <!-- 1. Product & Barcode Information -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-circle-info text-primary me-2"></i>1. Product &amp; Barcode Information
                </h5>
            </div>
            <div class="card-body p-4">
                
                <!-- Barcode Input (Scanner Friendly) -->
                <div class="mb-4 p-3 bg-light rounded border">
                    <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-barcode text-primary me-1"></i> Barcode (Scan with Gun Scanner or Type)</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle small fw-normal">
                            <i class="fa-solid fa-check me-1"></i>Scanner Ready
                        </span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white text-muted">
                            <i class="fa-solid fa-barcode fs-4"></i>
                        </span>
                        <input type="text" 
                               name="barcode" 
                               id="barcode" 
                               class="form-control" 
                               placeholder="Scan barcode with gun scanner or enter barcode..." 
                               value="{{ old('barcode') }}"
                               autofocus 
                               autocomplete="off">
                        <button type="button" class="btn btn-outline-primary px-3" id="btnGenBarcode" title="Generate random barcode">
                            <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Generate
                        </button>
                        <button type="button" class="btn btn-outline-secondary px-3" id="btnClearBarcode" title="Clear">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="fa-solid fa-info-circle text-info me-1"></i>
                        Point your barcode gun scanner and pull the trigger. Pressing Enter automatically moves to the Product Name field.
                    </small>
                </div>

                <!-- Product Name -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="productName"
                           class="form-control form-control-lg @error('name') is-invalid @enderror" 
                           placeholder="Enter product name (e.g. iPhone 15 Pro, Samsung Galaxy S24, Vivo V30)" 
                           value="{{ old('name') }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category & Brand -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select form-select-lg @error('category_id') is-invalid @enderror" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Brand</label>
                        <select name="brand_id" class="form-select form-select-lg">
                            <option value="">-- Select Brand (Optional) --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- SKU & Model Number -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">SKU (Item Code)</label>
                        <div class="input-group">
                            <input type="text" 
                                   name="sku" 
                                   id="skuInput"
                                   class="form-control" 
                                   placeholder="Enter SKU (leave empty to auto-generate)" 
                                   value="{{ old('sku') }}">
                            <button type="button" class="btn btn-outline-secondary" id="btnGenSku">
                                <i class="fa-solid fa-rotate me-1"></i> Auto SKU
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Model Number</label>
                        <input type="text" 
                               name="model_number" 
                               class="form-control" 
                               placeholder="Enter model number (e.g. A3106, SM-S928B)" 
                               value="{{ old('model_number') }}">
                    </div>
                </div>

            </div>
        </div>

        <!-- 2. Pricing & Stock -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-indian-rupee-sign text-success me-2"></i>2. Pricing &amp; Stock
                </h5>
            </div>
            <div class="card-body p-4">
                
                <!-- Prices Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Purchase Price (Cost)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="purchase_price" 
                                   class="form-control number-only" 
                                   placeholder="Enter purchase price" 
                                   value="{{ old('purchase_price') }}">
                        </div>
                        <small class="text-muted">Cost price from supplier</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Selling Price (Retail) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="selling_price" 
                                   class="form-control number-only fw-bold @error('selling_price') is-invalid @enderror" 
                                   placeholder="Enter selling price" 
                                   value="{{ old('selling_price') }}" 
                                   required>
                        </div>
                        <small class="text-muted">Customer retail price</small>
                        @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">MRP (Printed on Box)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="mrp" 
                                   class="form-control number-only" 
                                   placeholder="Enter MRP" 
                                   value="{{ old('mrp') }}">
                        </div>
                        <small class="text-muted">Maximum Retail Price</small>
                    </div>
                </div>

                <!-- Stock & Tax Row -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Initial Stock Quantity</label>
                        <input type="text" 
                               inputmode="numeric" 
                               name="stock_quantity" 
                               class="form-control number-only integer-only" 
                               placeholder="Enter opening stock quantity" 
                               value="{{ old('stock_quantity') }}">
                        <small class="text-muted">Current quantity in store</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Low Stock Warning Alert</label>
                        <input type="text" 
                               inputmode="numeric" 
                               name="min_stock" 
                               class="form-control number-only integer-only" 
                               placeholder="Enter alert quantity (e.g. 5)" 
                               value="{{ old('min_stock') }}">
                        <small class="text-muted">Alerts when stock drops to this level</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">GST / Tax (%)</label>
                        <div class="input-group">
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="tax" 
                                   class="form-control number-only" 
                                   placeholder="Enter tax percentage (e.g. 18)" 
                                   value="{{ old('tax') }}">
                            <span class="input-group-text bg-light">%</span>
                        </div>
                        <small class="text-muted">Applicable GST / Tax rate</small>
                    </div>
                </div>

            </div>
        </div>

        <!-- 3. Images, Details & Options -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-images text-info me-2"></i>3. Images &amp; Details
                </h5>
            </div>
            <div class="card-body p-4">
                
                <!-- Images Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Main Product Image</label>
                        <input type="file" 
                               name="main_image" 
                               id="mainImage" 
                               class="form-control" 
                               accept="image/*"
                               onchange="previewMainImage(this)">
                        <small class="text-muted">Primary photo displayed in POS and catalog</small>
                        <div id="mainImagePreviewBox" class="mt-2 d-none">
                            <img id="mainImagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 100px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Gallery Images (Multiple)</label>
                        <input type="file" 
                               name="gallery_images[]" 
                               class="form-control" 
                               accept="image/*" 
                               multiple>
                        <small class="text-muted">Upload additional product photos (optional)</small>
                    </div>
                </div>

                <!-- Warranty -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Warranty Period</label>
                    <input type="text" 
                           name="warranty_period" 
                           class="form-control" 
                           placeholder="Enter warranty (e.g. 1 Year Brand Warranty, 6 Months)" 
                           value="{{ old('warranty_period') }}">
                </div>

                <!-- Descriptions -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Short Description</label>
                    <textarea name="short_description" 
                              class="form-control" 
                              rows="2" 
                              placeholder="Enter short description or key highlights...">{{ old('short_description') }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Detailed Description</label>
                    <textarea name="description" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Enter detailed product description...">{{ old('description') }}</textarea>
                </div>

                <!-- Options / Flags -->
                <label class="form-label fw-bold d-block mb-2">Product Options:</label>
                <div class="row g-3 p-3 bg-light rounded border">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="status" value="1" class="form-check-input" id="optStatus" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="optStatus">Active Product</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="featured" value="1" class="form-check-input" id="optFeatured" {{ old('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="optFeatured">Featured Product</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="new_arrival" value="1" class="form-check-input" id="optNew" {{ old('new_arrival') ? 'checked' : '' }}>
                            <label class="form-check-label" for="optNew">New Arrival</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" name="requires_serial" value="1" class="form-check-input" id="optSerial" {{ old('requires_serial') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-primary" for="optSerial">
                                <i class="fa-solid fa-mobile-screen-button me-1"></i> IMEI/Serial Tracking
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 4. Action Buttons Below the Form -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-4 d-flex flex-wrap gap-3 align-items-center justify-content-between">
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary btn-lg px-4 fw-semibold" id="btnSubmit">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Product
                    </button>
                    <button type="submit" name="save_and_new" value="1" class="btn btn-success btn-lg px-4 fw-semibold">
                        <i class="fa-solid fa-plus-circle me-2"></i> Save &amp; Add Another
                    </button>
                </div>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="fa-solid fa-xmark me-1"></i> Cancel
                </a>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
    // Preview selected main image
    function previewMainImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#mainImagePreview').attr('src', e.target.result);
                $('#mainImagePreviewBox').removeClass('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        // Barcode Gun Scanner Enter Key handling:
        // Gun scanners type the barcode and hit Enter.
        // We prevent default form submission on the barcode field and jump focus to product name.
        $('#barcode').on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                $('#productName').focus().select();
            }
        });

        // Generate Random 12-digit Barcode
        $('#btnGenBarcode').on('click', function() {
            const randomBarcode = Math.floor(100000000000 + Math.random() * 900000000000).toString();
            $('#barcode').val(randomBarcode);
            $('#productName').focus();
        });

        // Clear Barcode
        $('#btnClearBarcode').on('click', function() {
            $('#barcode').val('').focus();
        });

        // Auto Generate SKU
        $('#btnGenSku').on('click', function() {
            const name = $('#productName').val().trim();
            let prefix = 'SKU';
            if (name.length >= 3) {
                prefix = name.substring(0, 4).toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (prefix.length < 3) prefix = 'SKU';
            }
            const rand = Math.random().toString(36).substring(2, 7).toUpperCase();
            $('#skuInput').val(prefix + '-' + rand);
        });

        // Disable button on submit to prevent double-click
        $('#productForm').on('submit', function() {
            $('#btnSubmit').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving...');
        });
    });
</script>
@endpush
@endsection
