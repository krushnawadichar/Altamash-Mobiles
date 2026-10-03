@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <h2 class="fw-bold"><i class="fa-solid fa-user-shield text-primary me-2"></i> Roles</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add Role</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Role Name</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td class="ps-4 fw-bold">{{ ucfirst($role->name) }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit"></i></a>
                                @if($role->name !== 'admin')
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


