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
            --primary: #0f172a; /* Slate 900 - Premium dark */
            --secondary: #3b82f6; /* Blue 500 - Tech accent */
            --accent: #f59e0b; /* Amber - Highlights */
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-weight: 800;
            color: var(--primary) !important;
            font-size: 1.5rem;
        }
        .nav-link {
            font-weight: 600;
            color: #475569 !important;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem !important;
            position: relative;
        }
        @media (min-width: 992px) {
            .nav-link::after {
                content: '';
                position: absolute;
                width: 0;
                height: 2px;
                bottom: 0;
                left: 50%;
                background-color: var(--secondary);
                transition: all 0.3s ease;
                transform: translateX(-50%);
            }
            .nav-link:hover::after, .nav-link.active::after {
                width: 80%;
            }
        }
        .nav-link:hover, .nav-link.active {
            color: var(--secondary) !important;
        }
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: white;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                padding: 1.5rem;
                box-shadow: 0 15px 30px rgba(0,0,0,0.1);
                border-bottom-left-radius: 1rem;
                border-bottom-right-radius: 1rem;
            }
            .nav-link {
                padding: 0.75rem 1rem !important;
                border-radius: 0.5rem;
                margin-bottom: 0.25rem;
            }
            .nav-link:hover, .nav-link.active {
                background-color: #f8fafc;
                padding-left: 1.5rem !important;
            }
            .navbar-custom {
                position: relative;
            }
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
            background-color: #020617; /* Ultra dark slate */
            color: #94a3b8;
            padding: 5rem 0 2rem;
            position: relative;
            overflow: hidden;
        }
        .footer::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
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
                <ul class="navbar-nav mx-auto mb-3 mb-lg-0 text-center text-lg-start">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Shop</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
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
                    <p class="mt-3 pe-md-4">Your ultimate destination for premium electronics, mobile devices, and expert repair services. Quality guaranteed since 2026.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white fs-5 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center hover-scale" style="width: 40px; height: 40px;"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="text-white fs-5 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center hover-scale" style="width: 40px; height: 40px;"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="text-white fs-5 bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center hover-scale" style="width: 40px; height: 40px;"><i class="fa-brands fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 ">
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
