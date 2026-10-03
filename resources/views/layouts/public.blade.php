<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ALTAMASH MOBILE - Quality Electronics Store')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #1e3a8a; /* Deep blue */
            --secondary: #fbbf24; /* Amber */
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .navbar-custom {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 800;
            color: var(--primary) !important;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 500;
            color: #4b5563 !important;
            transition: color 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
        }
        .btn-primary-custom {
            background-color: var(--primary);
            color: white;
            border: none;
        }
        .btn-primary-custom:hover {
            background-color: #152c6b;
            color: white;
        }
        .btn-secondary-custom {
            background-color: var(--secondary);
            color: #1f2937;
            font-weight: 600;
            border: none;
        }
        .btn-secondary-custom:hover {
            background-color: #f59e0b;
        }
        .footer {
            background-color: #111827;
            color: #9ca3af;
            padding: 3rem 0;
        }
        .footer h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        .footer a {
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer a:hover {
            color: white;
        }
    </style>
    @stack('styles')
</head>
<body>
    
    <!-- Topbar -->
    <div class="bg-dark text-white py-2 text-center text-md-start">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <small><i class="fa-solid fa-phone me-2"></i> 8956586537 &nbsp;|&nbsp; <i class="fa-solid fa-envelope me-2"></i> Altamashmobiles01@gmail.com</small>
            <small class="mt-2 mt-md-0">Free Shipping on orders over ₹10,000</small>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('build/assets/logos/altmash-logo.jpeg') }}" alt="ALTAMASH MOBILE Logo" style="height: 65px; object-fit: contain;" class="me-2">
                <span class="d-none d-sm-inline">ALTAMASH MOBILE</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Shop</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="text-dark fs-5 position-relative">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                    @auth
                        @if(auth()->user()->can('view dashboard'))
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-dark fw-bold">Admin Panel</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary-custom fw-bold">My Account</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark fw-bold px-3">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary-custom px-3">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6">
                    <h5 class="d-flex align-items-center mb-3">
                        <img src="{{ asset('build/assets/logos/altmash-logo.jpeg') }}" alt="ALTAMASH MOBILE Logo" style="height: 65px; object-fit: contain;" class="me-2">
                    </h5>
                    <p>Your trusted destination for premium electronics, home appliances, and gadgets. Quality service since 2026.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white fs-4"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('shop') }}">Shop</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Customer Service</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><a href="#">Track Order</a></li>
                        <li><a href="#">Return Policy</a></li>
                        <li><a href="#">Warranty Info</a></li>
                        <li><a href="#">Repair Services</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5>Store Info</h5>
                    <ul class="list-unstyled d-flex flex-column gap-2">
                        <li><i class="fa-solid fa-location-dot me-2"></i> New narsala road, <br>opposite Dhanashree apartment, Narsala, Nagpur, Maharashtra, 440034.</li>
                        <li><i class="fa-solid fa-phone me-2"></i> 8956586537</li>
                        <li><i class="fa-solid fa-envelope me-2"></i> Altamashmobiles01@gmail.com</li>
                        <li><i class="fa-solid fa-clock me-2"></i> Mon - Sun: 10:00 AM - 9:00 PM</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-5 border-secondary">
            <div class="text-center mt-4">
                <small>&copy; {{ date('Y') }} ALTAMASH MOBILE. All Rights Reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
