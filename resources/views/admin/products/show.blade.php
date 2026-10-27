@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb & Top Actions -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}" class="text-decoration-none">Products</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark">{{ $product->name }}</h2>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                <span class="badge bg-light text-secondary border font-monospace">SKU: {{ $product->sku ?? 'N/A' }}</span>
                @if($product->barcode)
                    <span class="badge bg-dark-subtle text-dark border font-monospace">
                        <i class="fa-solid fa-barcode text-primary me-1"></i>{{ $product->barcode }}
                    </span>
                @endif
                @if($product->status)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
                @if($product->requires_serial)
                    <span class="badge bg-primary-subtle text-primary border">
                        <i class="fa-solid fa-mobile-screen-button me-1"></i>IMEI/Serial Tracking
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Products
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Product
            </a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fa-solid fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Images & Barcode -->
        <div class="col-lg-5">
            <!-- Product Images Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-image text-primary me-2"></i>Product Media</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-3 bg-light rounded p-3 d-flex align-items-center justify-content-center" style="min-height: 280px;">
                        @if($product->mainImage)
                            @php $mainImgUrl = str_starts_with($product->mainImage->image, 'http') ? $product->mainImage->image : Storage::url($product->mainImage->image); @endphp
                            <img id="activeMainImg" src="{{ $mainImgUrl }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 260px; object-fit: contain;">
                        @else
                            <div class="text-muted text-center py-5">
                                <i class="fa-regular fa-image fs-1 d-block mb-2 text-secondary"></i>
                                <span>No Image Available</span>
                            </div>
                        @endif
                    </div>

                    @if($product->images && $product->images->count() > 0)
                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                            @foreach($product->images as $img)
                                @php $thumbUrl = str_starts_with($img->image, 'http') ? $img->image : Storage::url($img->image); @endphp
                                <img src="{{ $thumbUrl }}" 
                                     alt="Thumbnail" 
                                     class="img-thumbnail cursor-pointer {{ $img->is_main ? 'border-primary border-2' : '' }}" 
                                     style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                     onclick="document.getElementById('activeMainImg').src='{{ $thumbUrl }}'">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Barcode Label Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-barcode text-primary me-2"></i>Product Barcode</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="printBarcodeLabel()">
                        <i class="fa-solid fa-print me-1"></i> Print Label
                    </button>
                </div>
                <div class="card-body p-4 text-center" id="barcodePrintSection">
                    @if($product->barcode)
                        <div class="p-3 bg-light rounded border d-inline-block w-100">
                            <div class="fw-bold small text-dark mb-1">{{ config('app.name', 'Altamash Mobiles') }}</div>
                            <div class="fw-semibold text-truncate mb-2" style="max-width: 300px; margin: 0 auto;">{{ $product->name }}</div>
                            <svg id="productBarcodeSvg" style="max-width: 100%; height: 55px;"></svg>
                            <div class="fw-bold fs-5 text-primary mt-1">₹{{ number_format($product->selling_price, 2) }}</div>
                        </div>
                    @else
                        <div class="p-4 text-muted bg-light rounded border">
                            <i class="fa-solid fa-barcode fs-2 d-block mb-2 text-secondary"></i>
                            <span>No barcode assigned yet.</span>
                            <div class="mt-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-plus me-1"></i> Add Barcode
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Details, Pricing, Inventory -->
        <div class="col-lg-7">
            
            <!-- Pricing Summary Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-tag text-success me-2"></i>Pricing &amp; Margins</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded border text-center">
                                <small class="text-muted d-block fw-semibold">Selling Price</small>
                                <span class="fs-4 fw-bold text-primary">₹{{ number_format($product->selling_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded border text-center">
                                <small class="text-muted d-block fw-semibold">Purchase Cost</small>
                                <span class="fs-4 fw-bold text-dark">₹{{ number_format($product->purchase_price, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded border text-center">
                                <small class="text-muted d-block fw-semibold">MRP</small>
                                <span class="fs-4 fw-bold text-secondary">
                                    {{ $product->mrp ? '₹' . number_format($product->mrp, 2) : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @php
                        $profit = $product->selling_price - $product->purchase_price;
                        $marginPct = $product->selling_price > 0 ? ($profit / $product->selling_price) * 100 : 0;
                    @endphp
                    <div class="d-flex flex-wrap gap-4 mt-3 pt-3 border-top justify-content-around text-center">
                        <div>
                            <small class="text-muted d-block">Estimated Gross Profit</small>
                            <span class="fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹{{ number_format($profit, 2) }}
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Profit Margin</small>
                            <span class="fw-bold text-primary">{{ number_format($marginPct, 1) }}%</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">GST / Tax</small>
                            <span class="fw-bold text-dark">{{ $product->tax ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory & Classification Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-boxes-stacked text-warning me-2"></i>Inventory &amp; Organization</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 align-middle">
                            <tbody>
                                <tr>
                                    <th class="bg-light" style="width: 35%;">Category</th>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Brand</th>
                                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Model Number</th>
                                    <td>{{ $product->model_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Stock in Store</th>
                                    <td>
                                        @if($product->stock_quantity <= 0)
                                            <span class="badge bg-danger fs-6">Out of Stock (0)</span>
                                        @elseif($product->stock_quantity <= $product->min_stock)
                                            <span class="badge bg-warning text-dark fs-6">{{ $product->stock_quantity }} Units (Low Stock)</span>
                                        @else
                                            <span class="badge bg-success fs-6">{{ $product->stock_quantity }} Units Available</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Low Stock Alert Level</th>
                                    <td>{{ $product->min_stock }} Units</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Warranty</th>
                                    <td>{{ $product->warranty_period ?? 'No Warranty' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Serial / IMEI Tracking</th>
                                    <td>
                                        @if($product->requires_serial)
                                            <span class="text-primary fw-bold"><i class="fa-solid fa-check me-1"></i>Yes, IMEI/Serial tracking required</span>
                                        @else
                                            <span class="text-muted">Standard barcode tracking only</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Marketing Badges</th>
                                    <td>
                                        @if($product->featured) <span class="badge bg-warning text-dark me-1">Featured</span> @endif
                                        @if($product->new_arrival) <span class="badge bg-info text-dark me-1">New Arrival</span> @endif
                                        @if($product->best_seller) <span class="badge bg-danger me-1">Best Seller</span> @endif
                                        @if(!$product->featured && !$product->new_arrival && !$product->best_seller)
                                            <span class="text-muted">Standard Listing</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Descriptions Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-file-lines text-info me-2"></i>Product Descriptions</h5>
                </div>
                <div class="card-body p-4">
                    @if($product->short_description)
                        <div class="mb-3">
                            <h6 class="fw-bold text-muted small text-uppercase">Short Summary</h6>
                            <p class="text-dark">{{ $product->short_description }}</p>
                        </div>
                    @endif

                    <div>
                        <h6 class="fw-bold text-muted small text-uppercase">Detailed Description</h6>
                        @if($product->description)
                            <div class="text-muted" style="line-height: 1.7;">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        @else
                            <p class="text-muted fst-italic">No detailed description provided.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const barcodeVal = "{{ $product->barcode }}";
        if (barcodeVal) {
            try {
                JsBarcode("#productBarcodeSvg", barcodeVal, {
                    format: "CODE128",
                    lineColor: "#000",
                    width: 2,
                    height: 48,
                    displayValue: true,
                    fontSize: 14
                });
            } catch (e) {
                console.warn(e);
            }
        }
    });

    function printBarcodeLabel() {
        const printContent = document.getElementById('barcodePrintSection').innerHTML;
        const win = window.open('', '', 'height=400,width=450');
        win.document.write('<html><head><title>Print Barcode Label</title>');
        win.document.write('<style>body { font-family: sans-serif; text-align: center; margin: 20px; } svg { max-width: 100%; }</style>');
        win.document.write('</head><body>');
        win.document.write(printContent);
        win.document.write('</body></html>');
        win.document.close();
        win.focus();
        setTimeout(() => {
            win.print();
            win.close();
        }, 300);
    }
</script>
@endpush
@endsection
