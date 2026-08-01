@extends('layouts.app')

@section('title', 'Accessories - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-headphones me-2 text-primary"></i> Accessories
        </h2>
        <a href="{{ route('admin.accessories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Accessory
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
                            <th>Type</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accessories as $accessory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($accessory->image)
                                        <img src="{{ Storage::url($accessory->image) }}" 
                                             alt="{{ $accessory->name }}" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>{{ $accessory->name }}</td>
                                <td>{{ $accessory->type }}</td>
                                <td>{{ $accessory->sku }}</td>
                                <td>Rs. {{ number_format($accessory->selling_price, 0) }}</td>
                                <td>
                                    @if($accessory->current_stock <= 0)
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @elseif($accessory->current_stock <= $accessory->minimum_stock)
                                        <span class="badge bg-warning">Low Stock ({{ $accessory->current_stock }})</span>
                                    @else
                                        <span class="badge bg-success">{{ $accessory->current_stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($accessory->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.accessories.edit', $accessory->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.accessories.barcode', $accessory->id) }}" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-barcode"></i>
                                        </a>
                                        <form action="{{ route('admin.accessories.destroy', $accessory->id) }}" 
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
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No accessories found.</p>
                                    <a href="{{ route('admin.accessories.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Accessory
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