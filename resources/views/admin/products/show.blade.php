@extends('layouts.app')

@section('title', $product->name . ' - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-mobile-alt me-2 text-primary"></i> Product Details
        </h2>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Product Image -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="img-fluid rounded" style="max-height: 300px;">
                    @else
                        <div class="bg-light rounded p-5">
                            <i class="fas fa-mobile-alt fa-5x text-muted"></i>
                            <p class="text-muted mt-2">No Image</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Barcode -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body text-center">
                    <h6 class="fw-bold mb-2">Barcode</h6>
                    <div class="bg-light p-3 rounded">
                        {!! DNS1D::getBarcodeHTML($product->barcode, 'C128', 2, 60) !!}
                        <p class="mt-2 mb-0 small">{{ $product->barcode }}</p>
                    </div>
                    <a href="{{ route('admin.products.barcode', $product->id) }}" class="btn btn-sm btn-primary mt-2" target="_blank">
                        <i class="fas fa-print me-1"></i> Print Barcode
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="fw-bold mb-0">{{ $product->name }}</h4>
                    <span class="badge bg-info mt-1">{{ $product->sku }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Category</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Brand</td>
                                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Supplier</td>
                                    <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Unit</td>
                                    <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Product Type</td>
                                    <td>{{ $product->productType->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Mobile Company</td>
                                    <td>{{ $product->mobileCompany->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Purchase Price</td>
                                    <td class="fw-bold">Rs. {{ number_format($product->purchase_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Selling Price</td>
                                    <td class="fw-bold text-success">Rs. {{ number_format($product->selling_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">GST %</td>
                                    <td>{{ $product->gst_percentage }}%</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Tax Amount</td>
                                    <td>Rs. {{ number_format($product->tax_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Color</td>
                                    <td>{{ $product->color ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Storage</td>
                                    <td>{{ $product->storage ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">RAM</td>
                                    <td>{{ $product->ram ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <h6 class="text-muted">Current Stock</h6>
                                            <h3 class="fw-bold {{ $product->current_stock <= 0 ? 'text-danger' : ($product->current_stock <= $product->minimum_stock ? 'text-warning' : 'text-success') }}">
                                                {{ $product->current_stock }}
                                            </h3>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h6 class="text-muted">Minimum Stock</h6>
                                            <h3 class="fw-bold">{{ $product->minimum_stock }}</h3>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h6 class="text-muted">Status</h6>
                                            <h3>
                                                @if($product->current_stock <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @elseif($product->current_stock <= $product->minimum_stock)
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">In Stock</span>
                                                @endif
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($product->description)
                        <div class="mt-3">
                            <h6 class="fw-bold text-muted">Description</h6>
                            <p class="mb-0">{{ $product->description }}</p>
                        </div>
                    @endif

                    <!-- Meta Information -->
                    <div class="mt-3">
                        <small class="text-muted">
                            Created: {{ $product->created_at->format('d M, Y H:i') }} | 
                            Updated: {{ $product->updated_at->format('d M, Y H:i') }}
                            @if($product->creator)
                                | By: {{ $product->creator->name }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection