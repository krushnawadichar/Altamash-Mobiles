@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <h2 class="fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Add New User</h2>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Assign Roles</label>
                <div class="row g-2 mt-1">
                    @foreach($roles as $role)
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="form-check form-switch border p-2 rounded shadow-sm d-flex align-items-center bg-light">
                                <input class="form-check-input m-0 me-2 mt-0" type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->name }}" style="cursor: pointer;">
                                <label class="form-check-label user-select-none mb-0" for="role_{{ $role->id }}" style="cursor: pointer;">{{ ucfirst($role->name) }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Assign Menu Permissions</label>
                <div class="row g-2 mt-1">
                    @foreach($permissions as $permission)
                        @php 
                            $label = str_replace('view ', '', $permission->name); 
                            $label = ucwords($label);
                            if (strtolower($label) == 'Pos') $label = 'POS';
                        @endphp
                        <div class="col-md-3 col-sm-4 col-6">
                            <div class="form-check form-switch border p-2 rounded shadow-sm d-flex align-items-center">
                                <input class="form-check-input m-0 me-2 mt-0" type="checkbox" name="permissions[]" id="perm_{{ $permission->id }}" value="{{ $permission->name }}" style="cursor: pointer;">
                                <label class="form-check-label user-select-none mb-0" for="perm_{{ $permission->id }}" style="cursor: pointer;">{{ $label }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <hr>
            <div class="text-end">
                <a href="{{ route('admin.users.index') }}" class="btn btn-light me-2">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save User</button>
            </div>
        </form>
    </div>
</div>
@endsection


