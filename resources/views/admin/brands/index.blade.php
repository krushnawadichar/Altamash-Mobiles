@extends('layouts.admin')

@section('title', 'Brand Management')
@section('page_title', 'Phone & Gadget Brands')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Brands</h4>
            <small class="text-muted">Manage manufacturers and product brands</small>
        </div>
        <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createBrandModal">
            <i class="bi bi-plus-lg me-1"></i> Add Brand
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">Logo</th>
                        <th>Brand Name</th>
                        <th>Slug</th>
                        <th>Associated Products</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                    <tr>
                        <td>
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" class="rounded" width="36" height="36" style="object-fit: contain;">
                            @else
                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border fw-bold" style="width: 36px; height: 36px; font-size: 0.8rem;">
                                    {{ strtoupper(substr($brand->name, 0, 2)) }}
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">{{ $brand->name }}</td>
                        <td><code>{{ $brand->slug }}</code></td>
                        <td><span class="badge bg-light text-dark border">{{ $brand->products_count }} Products</span></td>
                        <td>
                            <span class="badge {{ $brand->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($brand->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border py-0 px-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $brand->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border py-0 px-2">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $brand->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Brand</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Brand Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Change Logo</label>
                                            <input type="file" name="logo" class="form-control" accept="image/*">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" {{ $brand->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $brand->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No brands found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($brands->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $brands->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Create Brand Modal -->
<div class="modal fade" id="createBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Motorola or Google" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Brand Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
