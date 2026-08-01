@extends('layouts.app')

@section('title', 'Brands - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-copyright me-2 text-primary"></i> Brands
        </h2>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Brand
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $brand)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($brand->logo)
                                        <img src="{{ Storage::url($brand->logo) }}" 
                                             alt="{{ $brand->name }}" 
                                             style="width: 40px; height: 40px; object-fit: contain;">
                                    @else
                                        <span class="text-muted">No Logo</span>
                                    @endif
                                </td>
                                <td>{{ $brand->name }}</td>
                                <td>{{ $brand->slug }}</td>
                                <td>{{ Str::limit($brand->description, 50) }}</td>
                                <td>
                                    @if($brand->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $brand->created_at->format('d M, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.brands.edit', $brand->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.brands.destroy', $brand->id) }}" 
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
                                    <p class="text-muted">No brands found.</p>
                                    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Brand
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