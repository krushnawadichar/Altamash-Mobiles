<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventory Management</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body{
            background: linear-gradient(135deg,#4e73df,#224abe);
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            font-family:Arial, Helvetica, sans-serif;
        }

        .login-card{
            width:100%;
            max-width:420px;
            background:#fff;
            border-radius:15px;
            padding:40px;
            box-shadow:0 15px 35px rgba(0,0,0,.2);
        }

        .logo{
            width:70px;
            height:70px;
            background:#4e73df;
            color:#fff;
            display:flex;
            justify-content:center;
            align-items:center;
            border-radius:50%;
            font-size:30px;
            margin:auto;
            margin-bottom:20px;
        }

        .form-control{
            height:50px;
            border-radius:10px;
        }

        .btn-login{
            height:50px;
            border-radius:10px;
            font-size:18px;
        }

        .login-title{
            font-weight:700;
        }

        a{
            text-decoration:none;
        }
    </style>

</head>
<body>

<div class="login-card">

    <div class="text-center">

        <div class="logo">
            <i class="bi bi-person-fill"></i>
        </div>

        <h3 class="login-title">Welcome Back</h3>

        <p class="text-muted">
            Login to your account
        </p>

    </div>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">

            <label class="form-label">
                Email Address
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Enter Email"
                required
                autofocus>

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="mb-3">

            <label class="form-label">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Enter Password"
                required>

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="d-flex justify-content-between mb-3">

            <div class="form-check">

                <input class="form-check-input"
                       type="checkbox"
                       name="remember"
                       id="remember">

                <label class="form-check-label" for="remember">
                    Remember Me
                </label>

            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    Forgot Password?
                </a>
            @endif

        </div>

        <button class="btn btn-primary w-100 btn-login">
            <i class="bi bi-box-arrow-in-right"></i>
            Login
        </button>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>