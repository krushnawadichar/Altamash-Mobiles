@extends('layouts.public')

@section('title', 'Login - ALTAMASH MOBILE')

@push('styles')
<style>
    .login-container {
        min-height: calc(100vh - 250px);
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 3rem 0;
    }
    .login-card {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        background: #ffffff;
    }
    .login-image {
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.85) 0%, rgba(15, 30, 75, 0.95) 100%), url('https://images.unsplash.com/photo-1555680202-c86f0e12f086?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        padding: 3rem;
        text-align: center;
        position: relative;
    }
    .login-image::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23ffffff" fill-opacity="0.05" fill-rule="evenodd"%3E%3Ccircle cx="3" cy="3" r="3"/%3E%3Ccircle cx="13" cy="13" r="3"/%3E%3C/g%3E%3C/svg%3E');
    }
    .login-image-content {
        position: relative;
        z-index: 2;
    }
    .login-form-wrapper {
        padding: 4rem;
        background: #ffffff;
    }
    .input-group-text {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #6b7280;
    }
    .form-control {
        padding: 0.875rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .form-control:focus {
        background-color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(30, 58, 138, 0.15);
    }
    .btn-login {
        background-color: var(--primary);
        color: white;
        padding: 0.875rem;
        border-radius: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.2s ease;
    }
    .btn-login:hover {
        background-color: #152c6b;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(30, 58, 138, 0.3);
        color: white;
    }
    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    @media (max-width: 991.98px) {
        .login-form-wrapper {
            padding: 3rem 2rem;
        }
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10 col-md-12">
                <div class="card login-card">
                    <div class="row g-0">
                        <div class="col-md-5 d-none d-md-block login-image">
                            <div class="login-image-content">
                                <div class="mb-4">
                                    <i class="fa-solid fa-bolt text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <h2 class="fw-bold mb-3">ALTAMASH MOBILE</h2>
                                <p class="mb-0" style="color: #cbd5e1; font-weight: 300;">Manage your electronics business efficiently with our powerful point of sale and inventory management system.</p>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="login-form-wrapper">
                                <div class="text-center mb-4 d-md-none">
                                    <i class="fa-solid fa-bolt text-warning fs-1 mb-2"></i>
                                    <h2 class="fw-bold text-primary mb-1">ALTAMASH MOBILE</h2>
                                    <p class="text-muted">Welcome back</p>
                                </div>
                                
                                <h3 class="fw-bold text-dark mb-1 d-none d-md-block">Welcome Back!</h3>
                                <p class="text-muted mb-4 d-none d-md-block">Please sign in to your account to continue.</p>

                                <!-- Session Status -->
                                @if (session('status'))
                                    <div class="alert alert-success mb-4 border-0 bg-success bg-opacity-10 text-success fw-medium rounded-3" role="alert">
                                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Email Address -->
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0"><i class="fa-solid fa-envelope"></i></span>
                                            <input id="email" type="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="name@example.com">
                                        </div>
                                        @error('email')
                                            <div class="text-danger mt-1 small fw-medium"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="password" class="form-label fw-semibold text-dark mb-0">Password</label>
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="text-decoration-none text-primary small fw-medium">Forgot password?</a>
                                            @endif
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0"><i class="fa-solid fa-lock"></i></span>
                                            <input id="password" type="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="••••••••">
                                        </div>
                                        @error('password')
                                            <div class="text-danger mt-1 small fw-medium"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="mb-4 form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                        <label class="form-check-label text-muted user-select-none" for="remember_me">
                                            Remember me for 30 days
                                        </label>
                                    </div>

                                    <div class="d-grid mt-4 pt-2">
                                        <button type="submit" class="btn btn-login btn-lg d-flex justify-content-center align-items-center gap-2">
                                            <span>Sign In</span>
                                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


