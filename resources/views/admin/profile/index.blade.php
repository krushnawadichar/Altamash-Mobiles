@extends('layouts.admin')

@section('title', 'My Profile')
@section('page_title', 'User Profile Settings')

@section('content')
<div class="container-fluid px-0">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-person-circle me-1 text-primary"></i>My Account Information</span>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle bg-primary text-white fs-3 fw-bold d-flex align-items-center justify-content-center border" style="width: 70px; height: 70px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">{{ auth()->user()->name }}</h5>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-capitalize">{{ auth()->user()->role }}</span>
                                <small class="text-muted d-block mt-1">Username: <code>@<span>{{ auth()->user()->username }}</span></code></small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Mobile Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Profile Avatar</label>
                                <input type="file" name="avatar" class="form-control" accept="image/*">
                            </div>

                            <div class="col-12 border-top pt-3 mt-3">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-lock me-1 text-secondary"></i>Change Password</h6>
                                <p class="text-muted small">Leave password fields blank if you do not want to change your current password.</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat new password">
                            </div>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-check-circle-fill me-1"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
