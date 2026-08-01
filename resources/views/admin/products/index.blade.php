@extends('layouts.app')

@section('title', 'Products - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-mobile-alt me-2 text-primary"></i> Products
        </h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Product
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" 
                                             alt="{{ $product->name }}" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>Rs. {{ number_format($product->selling_price, 0) }}</td>
                                <td>
                                    @if($product->current_stock <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($product->current_stock <= $product->minimum_stock)
                                        <span class="badge bg-warning">Low Stock ({{ $product->current_stock }})</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->current_stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.products.show', $product->id) }}" 
                                           class="btn btn-info text-white me-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-warning me-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.products.barcode', $product->id) }}" 
                                           class="btn btn-secondary me-2">
                                            <i class="fas fa-barcode"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No products found.</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Product
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection