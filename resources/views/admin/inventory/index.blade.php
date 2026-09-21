@extends('layouts.admin')

@section('title', 'Inventory Stock Overview')
@section('page_title', 'Inventory & Stock Management')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Stats -->
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Products</div>
                <h4 class="fw-bold mb-0 mt-1">{{ $products->total() }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Stock Units</div>
                <h4 class="fw-bold mb-0 mt-1 text-primary">{{ $products->sum('current_stock') }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Low Stock Items</div>
                <h4 class="fw-bold mb-0 mt-1 text-warning">
                    {{ \App\Models\Product::whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0)->count() }}
                </h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Out of Stock Items</div>
                <h4 class="fw-bold mb-0 mt-1 text-danger">
                    {{ \App\Models\Product::where('current_stock', '<=', 0)->count() }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.inventory.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search product name, SKU, barcode, IMEI..." value="{{ request('search') }}">
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
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Stock Status</option>
                        <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Product & SKU</th>
                        <th>Type</th>
                        <th>Brand / Category</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Available Stock</th>
                        <th>Alert Limit</th>
                        <th>Stock Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            <a href="{{ route('admin.products.show', $product) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                            <div class="text-muted small">SKU: <code>{{ $product->sku }}</code> &bull; Barcode: {{ $product->barcode ?: '-' }}</div>
                        </td>
                        <td>
                            @if($product->type === 'mobile')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-phone me-1"></i>Mobile</span>
                            @elseif($product->type === 'spare_part')
                                <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-wrench me-1"></i>Part</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle"><i class="bi bi-headphones me-1"></i>Accessory</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $product->brand?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $product->category?->name ?? 'General' }}</small>
                        </td>
                        <td>₹{{ number_format($product->purchase_price, 2) }}</td>
                        <td class="fw-bold text-success">₹{{ number_format($product->selling_price, 2) }}</td>
                        <td>
                            <span class="fs-6 fw-bold {{ $product->current_stock <= $product->min_stock ? 'text-danger' : 'text-dark' }}">
                                {{ $product->current_stock }}
                            </span>
                        </td>
                        <td>{{ $product->min_stock }}</td>
                        <td>
                            <span class="badge {{ $product->stock_badge_class }}">
                                {{ $product->stock_status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.purchases.create') }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Order Stock">
                                <i class="bi bi-plus"></i> Reorder
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No inventory records matching query.</td>
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
