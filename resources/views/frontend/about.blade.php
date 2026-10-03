@extends('layouts.public')

@section('title', 'About Us - ' . config('app.name', 'ALTAMASH MOBILE'))

@section('content')
<!-- Page Header -->
<div class="bg-primary text-white py-5" style="background: linear-gradient(rgba(30, 58, 138, 0.9), rgba(30, 58, 138, 0.9)), url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;">
    <div class="container py-5 text-center">
        <h1 class="display-4 fw-bold mb-3 animate-fade-up">About Us</h1>
        <p class="lead mb-0 text-white-50">Your Trusted Destination for Premium Electronics</p>
    </div>
</div>

<!-- Our Story -->
<div class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Our Team" class="img-fluid rounded-4 shadow-lg">
            </div>
            <div class="col-lg-6">
                <span class="text-primary fw-bold text-uppercase tracking-wider">Our Story</span>
                <h2 class="fw-bold mb-4 display-6">Who We Are</h2>
                <p class="fs-5 text-muted mb-4">Established in 2026, <strong>ALTAMASH MOBILE</strong> has grown to become a leading destination for premium electronics, mobile devices, and reliable repair services in Nagpur.</p>
                <p class="text-muted mb-4">We believe that technology should be accessible, affordable, and supported by experts. That's why we don't just sell products; we build relationships with our customers to ensure they get the right device for their needs and the support to keep it running perfectly.</p>
                
                <div class="row g-4 mt-2">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                                <i class="fa-solid fa-users fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold m-0">10k+</h4>
                                <span class="text-muted small">Happy Customers</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                                <i class="fa-solid fa-box-open fs-3"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold m-0">5k+</h4>
                                <span class="text-muted small">Products Sold</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Our Values -->
<div class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase tracking-wider">What Drives Us</span>
            <h2 class="fw-bold mb-3 display-6">Our Core Values</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center rounded-4 transition-all hover-card">
                    <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-gem fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Quality First</h4>
                    <p class="text-muted mb-0">We only source genuine, high-quality products from trusted brands. We never compromise on the quality of what we sell.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center rounded-4 transition-all hover-card">
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-handshake fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Customer Trust</h4>
                    <p class="text-muted mb-0">Transparency and honesty are at the heart of our business. We build long-lasting relationships based on trust.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4 text-center rounded-4 transition-all hover-card">
                    <div class="icon-circle bg-info bg-opacity-10 text-info mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-bolt fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Expert Service</h4>
                    <p class="text-muted mb-0">Our team of certified technicians provides fast, reliable, and affordable repair services for all your devices.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visit Us -->
<div class="py-5 bg-white border-top">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4 display-6">Visit Our Store</h2>
                <p class="lead text-muted mb-5">Experience our wide range of products in person. Our friendly staff is always ready to help you find exactly what you need.</p>
                
                <ul class="list-unstyled d-flex flex-column gap-4">
                    <li class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-location-dot fs-4 text-primary mt-1"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Store Address</h5>
                            <p class="text-muted m-0">New narsala road, opposite Dhanashree apartment,<br>Narsala, Nagpur, Maharashtra, 440034.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-clock fs-4 text-primary mt-1"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Opening Hours</h5>
                            <p class="text-muted m-0">Monday - Sunday: 10:00 AM - 9:00 PM</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-3">
                        <i class="fa-solid fa-phone fs-4 text-primary mt-1"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Contact Us</h5>
                            <p class="text-muted m-0">8956586537<br>Altamashmobiles01@gmail.com</p>
                        </div>
                    </li>
                </ul>
                <div class="mt-4">
                    <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">Get In Touch</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="rounded-4 overflow-hidden shadow-lg" style="height: 400px; background-color: #f1f3f4;">
                    <!-- A placeholder for Google Maps, currently using an image -->
                    <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Location Map" class="w-100 h-100 object-fit-cover opacity-75">
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .tracking-wider { letter-spacing: 2px; }
    .transition-all { transition: all 0.3s ease; }
    .hover-card:hover { 
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important;
    }
    .animate-fade-up {
        animation: fadeUp 1s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
    }
    @keyframes fadeUp {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .object-fit-cover { object-fit: cover; }
</style>
@endpush
@endsection
