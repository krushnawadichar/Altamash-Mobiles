@extends('layouts.admin')

@section('title', 'Staff & User Management')
@section('page_title', 'Staff & User Roles')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Staff & Users</h4>
            <small class="text-muted">Manage system administrators, showroom salesmen, and workshop technicians</small>
        </div>
        <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="bi bi-person-plus me-1"></i> Add New Staff
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Email / Username</th>
                        <th>Phone Number</th>
                        <th>Assigned Role</th>
                        <th>Account Status</th>
                        <th>Created At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="fw-bold text-dark">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            <small class="text-muted"><code>@<span>{{ $user->username }}</span></code></small>
                        </td>
                        <td>{{ $user->phone ?: '-' }}</td>
                        <td>
                            @php
                                $roleBadge = match($user->role) {
                                    'admin' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    'salesman' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    'technician' => 'bg-info-subtle text-info border border-info-subtle',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $roleBadge }} text-capitalize px-2 py-1">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td><small class="text-muted">{{ $user->created_at->format('d M Y') }}</small></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border py-0 px-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border py-0 px-2"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Staff: {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Full Name</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Email Address</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Phone</label>
                                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Role</label>
                                            <select name="role" class="form-select">
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                                                <option value="salesman" {{ $user->role === 'salesman' ? 'selected' : '' }}>Salesman (POS & Sales)</option>
                                                <option value="technician" {{ $user->role === 'technician' ? 'selected' : '' }}>Technician (Repairs)</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold small">Reset Password (leave empty to keep)</label>
                                            <input type="password" name="password" class="form-control" placeholder="New password">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary fw-bold">Update Staff</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="e.g. john_sales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mobile Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Assign Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="salesman" selected>Salesman (POS Billing & Sales)</option>
                            <option value="technician">Technician (Repairs & Job Cards)</option>
                            <option value="admin">Administrator (Full Control)</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Staff Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
