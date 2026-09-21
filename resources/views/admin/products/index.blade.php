@extends('layouts.admin')

@section('title', 'Product Management')
@section('page_title', 'Product Catalog & Inventory')

@section('content')
<div class="container-fluid px-0">

    <!-- Header Actions & Filters -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Products</h4>
            <p class="text-muted small mb-0">Manage smartphone models, accessories, and spare parts inventory</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm fw-bold">
                <i class="bi bi-plus-lg"></i> Add New Product
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search name, SKU, barcode, IMEI..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="mobile" {{ request('type') == 'mobile' ? 'selected' : '' }}>Mobile Phones</option>
                        <option value="accessory" {{ request('type') == 'accessory' ? 'selected' : '' }}>Accessories</option>
                        <option value="spare_part" {{ request('type') == 'spare_part' ? 'selected' : '' }}>Spare Parts</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="brand_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="stock_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-light border w-100" title="Clear Filters">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">Img</th>
                        <th>Product Details</th>
                        <th>Type</th>
                        <th>Brand / Category</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Current Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="rounded" width="42" height="42" style="object-fit: cover;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border" style="width: 42px; height: 42px;">
                                    <i class="bi bi-image fs-5"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                            <div class="text-muted small">
                                <span>SKU: <code>{{ $product->sku }}</code></span>
                                @if($product->model_no)
                                    &bull; <span>Model: {{ $product->model_no }}</span>
                                @endif
                                @if($product->barcode)
                                    &bull; <span>Barcode: {{ $product->barcode }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($product->type === 'mobile')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-phone me-1"></i>Mobile</span>
                            @elseif($product->type === 'spare_part')
                                <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-wrench me-1"></i>Spare Part</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-headphones me-1"></i>Accessory</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $product->brand?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $product->category?->name ?? 'General' }}</small>
                        </td>
                        <td class="text-secondary">₹{{ number_format($product->purchase_price, 2) }}</td>
                        <td class="fw-bold text-success">₹{{ number_format($product->selling_price, 2) }}</td>
                        <td>
                            <span class="badge {{ $product->stock_badge_class }} px-2 py-1">
                                {{ $product->current_stock }} ({{ $product->stock_status }})
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border py-0 px-2" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item" href="{{ route('admin.products.show', $product) }}"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product) }}"><i class="bi bi-pencil me-2"></i>Edit Product</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.products.barcode', $product) }}"><i class="bi bi-upc-scan me-2"></i>Print Barcode</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No products found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $products->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
