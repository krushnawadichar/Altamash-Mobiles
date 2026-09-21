<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ \App\Models\Setting::get('shop_name', 'MobileCare POS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            color: #ffffff;
        }

        .brand-icon-box {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .login-body {
            padding: 2.25rem 2rem 2rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #cbd5e1;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 0.85rem;
            border-radius: 10px;
            width: 100%;
            font-size: 1rem;
            letter-spacing: -0.01em;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .demo-credential-card {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.82rem;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-icon-box">
                <i class="bi bi-phone"></i>
            </div>
            <h4 class="fw-bold mb-1">{{ \App\Models\Setting::get('shop_name', 'MobileCare POS') }}</h4>
            <p class="mb-0 text-white-50 small">Mobile Shop Management & POS Software</p>
        </div>

        <div class="login-body">
            @if(session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small">Email or Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="login" class="form-control border-start-0" placeholder="admin@mobileshop.com or admin" value="{{ old('login', 'admin@mobileshop.com') }}" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold text-secondary small mb-0">Password</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" value="password" required>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                        <label class="form-check-label text-muted small" for="rememberMe">
                            Remember Me
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    Sign In to Dashboard <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </form>

            <div class="demo-credential-card">
                <div class="fw-bold text-dark mb-1"><i class="bi bi-info-circle-fill text-primary me-1"></i> Demo Credentials:</div>
                <div class="text-secondary">
                    <strong>Admin:</strong> <code>admin@mobileshop.com</code> | <code>password</code><br>
                    <strong>Sales:</strong> <code>sales@mobileshop.com</code> | <code>password</code><br>
                    <strong>Tech:</strong> <code>tech@mobileshop.com</code> | <code>password</code>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
