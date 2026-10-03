@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2 class="fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i> Add New Role</h2>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            
            <hr>
            <div class="text-end">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save Role</button>
            </div>
        </form>
    </div>
</div>
@endsection


