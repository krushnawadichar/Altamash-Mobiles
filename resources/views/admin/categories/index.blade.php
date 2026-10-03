@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Categories</h2>
        <p class="text-muted">Manage product categories</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i> Add Category</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table datatable table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Parent Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" width="50" class="img-thumbnail">
                        @else
                            <span class="text-muted">No Image</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $category->name }}</td>
                    <td>
                        @if($category->parent_id)
                            <span class="badge bg-secondary">{{ $category->parent->name ?? 'Unknown' }}</span>
                        @else
                            <span class="text-muted">Main Category</span>
                        @endif
                    </td>
                    <td>
                        @if($category->status)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit"></i></a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</div>
@endsection


