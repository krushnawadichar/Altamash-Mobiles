@extends('layouts.public')

@section('content')
<!-- Ultra Premium Hero Carousel -->
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-indicators mb-4">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    
    <div class="carousel-inner">
        <!-- Slide 1: New Mobiles -->
        <div class="carousel-item active">
            <div class="carousel-img-wrapper">
                <div class="carousel-gradient-overlay"></div>
                <!-- Premium new mobile image -->
                <img src="https://images.pexels.com/photos/404280/pexels-photo-404280.jpeg?auto=compress&cs=tinysrgb&w=1920&q=80" class="d-block w-100" alt="Latest Smartphones">
            </div>
            <div class="carousel-caption d-flex align-items-center justify-content-center justify-content-lg-start text-center text-lg-start h-100 px-lg-5">
                <div class="animate-fade-right container">
                    <div class="max-w-lg mx-auto mx-lg-0">
                        <span class="badge bg-white text-dark px-3 py-2 fs-6 rounded-pill mb-2 mb-md-3 shadow-sm text-uppercase tracking-wider fw-bold">LATEST & ORIGINAL</span>
                        <h1 class="hero-title fw-bolder text-white mb-2 mb-md-3 lh-1">Upgrade to the Latest <br class="d-none d-md-block"><span style="color: #f59e0b;">Smartphones</span></h1>
                        <p class="lead text-white-50 mb-3 mb-md-4 fs-5 fw-light d-none d-md-block">Explore the latest smartphones from top brands at competitive prices, with trusted service you can count on.</p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start mt-2 mt-md-4">
                            <a href="{{ route('shop') }}" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-lg hover-scale text-dark px-4 py-3 w-100 w-sm-auto">Shop New Mobiles</a>
                            <a href="#categories" class="btn btn-outline-light btn-lg rounded-pill fw-bold hover-scale px-4 py-3 w-100 w-sm-auto">Explore Brands</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 2: Second Hand Mobiles -->
        <div class="carousel-item">
            <div class="carousel-img-wrapper">
                <div class="carousel-gradient-overlay"></div>
                <!-- High quality used phones / stack image -->
                <img src="https://images.pexels.com/photos/699122/pexels-photo-699122.jpeg?auto=compress&cs=tinysrgb&w=1920&q=80" class="d-block w-100" alt="Second Hand Mobiles">
            </div>
            <div class="carousel-caption d-flex align-items-center justify-content-center justify-content-lg-end text-center text-lg-end h-100 px-lg-5">
                <div class="animate-fade-left container d-flex justify-content-center justify-content-lg-end">
                    <div class="max-w-lg">
                        <span class="badge bg-white text-dark px-3 py-2 fs-6 rounded-pill mb-2 mb-md-3 shadow-sm text-uppercase tracking-wider fw-bold">CERTIFIED PRE-OWNED</span>
                        <h1 class="hero-title fw-bolder text-white mb-2 mb-md-3 lh-1">Premium Phones. <br class="d-none d-md-block"><span style="color: #f59e0b;">Smarter Prices.</span></h1>
                        <p class="lead text-white-50 mb-3 mb-md-4 fs-5 fw-light d-none d-md-block">Quality pre-owned smartphones, thoroughly checked and ready to deliver great performance without the premium price.</p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-end mt-2 mt-md-4">
                            <a href="{{ route('shop') }}" class="btn btn-light btn-lg rounded-pill fw-bold shadow-lg hover-scale text-dark px-4 py-3 w-100 w-sm-auto">View Used Mobiles</a>
                            <a href="{{ route('shop') }}" class="btn btn-outline-light btn-lg rounded-pill fw-bold hover-scale px-4 py-3 w-100 w-sm-auto">Check Stock</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slide 3: Repairs and Accessories -->
        <div class="carousel-item">
            <div class="carousel-img-wrapper">
                <div class="carousel-gradient-overlay center-gradient"></div>
                <!-- Professional mobile repair image -->
                <img src="https://images.unsplash.com/photo-1597872253372-c741ef0b40eb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" class="d-block w-100" alt="Repairs and Accessories">
            </div>
            <div class="carousel-caption d-flex align-items-center justify-content-center text-center h-100 px-lg-5">
                <div class="animate-fade-up container">
                    <div class="mx-auto" style="max-width: 800px;">
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill mb-2 mb-md-3 shadow-sm text-uppercase tracking-wider fw-bold">EXPERT MOBILE SERVICE</span>
                        <h1 class="hero-title fw-bolder text-white mb-2 mb-md-3 lh-1">Your Phone, <br class="d-none d-md-block"><span style="color: #f59e0b;">Our Expertise.</span></h1>
                        <p class="lead text-white-50 mb-3 mb-md-4 fs-5 fw-light d-none d-md-block">From screen damage to battery issues, get reliable mobile repairs backed by experienced technicians and quality parts.</p>
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-2 mt-md-4">
                            <a href="{{ route('contact') }}" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-lg hover-scale text-dark px-4 py-3 w-100 w-sm-auto">Book a Repair</a>
                            <a href="{{ route('shop') }}" class="btn btn-outline-light btn-lg rounded-pill fw-bold hover-scale px-4 py-3 w-100 w-sm-auto">Shop Accessories</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Clean Features Section -->
<div class="py-5 bg-white border-bottom">
    <div class="container py-3">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3">
                    <div class="text-primary mb-3"><i class="fa-solid fa-truck-fast fs-1"></i></div>
                    <h5 class="fw-bold mb-1">Free Delivery</h5>
                    <p class="text-muted small mb-0 d-none d-sm-block">On orders over ₹10,000</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-start border-light">
                    <div class="text-primary mb-3"><i class="fa-solid fa-shield-halved fs-1"></i></div>
                    <h5 class="fw-bold mb-1">1 Year Warranty</h5>
                    <p class="text-muted small mb-0 d-none d-sm-block">Genuine Brand Products</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-start border-light">
                    <div class="text-primary mb-3"><i class="fa-solid fa-screwdriver-wrench fs-1"></i></div>
                    <h5 class="fw-bold mb-1">Expert Repair</h5>
                    <p class="text-muted small mb-0 d-none d-sm-block">Reliable & Fast Service</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 border-start border-light">
                    <div class="text-primary mb-3"><i class="fa-solid fa-headset fs-1"></i></div>
                    <h5 class="fw-bold mb-1">24/7 Support</h5>
                    <p class="text-muted small mb-0 d-none d-sm-block">Always Here to Help</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Shop by Category -->
<div class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold m-0 display-6">Shop By Category</h2>
            <div class="mx-auto mt-2 bg-primary" style="height: 3px; width: 60px;"></div>
        </div>
        
        <div class="row g-4 justify-content-center">
            @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="card h-100 text-center border-0 shadow-sm category-card transition-all bg-white">
                        <div class="card-body py-4">
                            @php
                                $icon = 'fa-box';
                                if(str_contains(strtolower($category->name), 'mobile') || str_contains(strtolower($category->name), 'phone')) $icon = 'fa-mobile-screen-button';
                                elseif(str_contains(strtolower($category->name), 'laptop') || str_contains(strtolower($category->name), 'computer')) $icon = 'fa-laptop';
                                elseif(str_contains(strtolower($category->name), 'tv') || str_contains(strtolower($category->name), 'television')) $icon = 'fa-tv';
                                elseif(str_contains(strtolower($category->name), 'audio') || str_contains(strtolower($category->name), 'headphone')) $icon = 'fa-headphones';
                                elseif(str_contains(strtolower($category->name), 'watch')) $icon = 'fa-clock';
                                elseif(str_contains(strtolower($category->name), 'camera')) $icon = 'fa-camera';
                            @endphp
                            <div class="icon-box text-primary mb-3 transition-all">
                                <i class="fa-solid {{ $icon }} fs-2"></i>
                            </div>
                            <h6 class="text-dark fw-bold m-0">{{ $category->name }}</h6>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-2 display-6">Top Selling Products</h2>
            <div class="mx-auto mt-2 bg-primary mb-4" style="height: 3px; width: 60px;"></div>
            <p class="text-muted max-w-lg mx-auto">Discover our most popular electronic devices loved by our customers.</p>
        </div>
        
        <div class="row g-4">
            @forelse($featuredProducts as $product)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm product-card transition-all">
                    <div class="position-relative p-4 text-center bg-light" style="height: 250px;">
                        @if($product->images->count() > 0)
                            @php $mainImage = $product->images->first()->image; @endphp
                            <img src="{{ str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/' . $mainImage) }}" class="img-fluid h-100 object-fit-contain transition-all product-img" alt="{{ $product->name }}">
                        @else
                            <img src="https://placehold.co/600x600/f8f9fa/a3a3a3?text=No+Image" class="img-fluid h-100 object-fit-contain transition-all product-img p-4" alt="No Image Available">
                        @endif
                    </div>
                    
                    <div class="card-body bg-white pt-3">
                        <small class="text-secondary fw-bold">{{ $product->category->name ?? 'Category' }}</small>
                        <h5 class="card-title fw-bold mt-1 mb-2 text-truncate">
                            <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                        </h5>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="price-wrap">
                                <span class="fw-bold fs-5 text-primary">₹{{ number_format($product->selling_price, 2) }}</span>
                            </div>
                            <a href="{{ route('product.show', $product->slug) }}" class="btn btn-outline-dark btn-sm rounded-circle" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">No trending products available.</p>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('shop') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold hover-scale">View All Collection</a>
        </div>
    </div>
</div>

<!-- Customer Reviews Section -->
<div class="py-5 bg-light">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase tracking-wider">Testimonials</span>
            <h2 class="fw-bold mb-3 display-6">What Our Customers Say</h2>
            <div class="mx-auto mt-2 bg-primary mb-4" style="height: 3px; width: 60px;"></div>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 review-card">
                    <div class="d-flex text-warning mb-3 fs-5">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="fst-italic text-muted mb-4 flex-grow-1">"Bought my new iPhone 15 Pro Max from here. Excellent customer service, got a great exchange value for my old phone, and the staff transferred all my data seamlessly."</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow" style="width: 50px; height: 50px;">R</div>
                        <div>
                            <h6 class="fw-bold m-0 text-dark">Rahul Sharma</h6>
                            <span class="text-muted small">Verified Buyer</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 review-card">
                    <div class="d-flex text-warning mb-3 fs-5">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>
                    <p class="fst-italic text-muted mb-4 flex-grow-1">"My laptop screen was completely shattered. The team at Altamash Mobile repaired it in just 2 days. It looks brand new now and they gave me a 6-month warranty on the part."</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow" style="width: 50px; height: 50px;">A</div>
                        <div>
                            <h6 class="fw-bold m-0 text-dark">Anjali Desai</h6>
                            <span class="text-muted small">Repair Service</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4 review-card">
                    <div class="d-flex text-warning mb-3 fs-5">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <p class="fst-italic text-muted mb-4 flex-grow-1">"The best electronics store in Nagpur! Highly recommend them for their transparent pricing and huge variety of products. Always my go-to place for tech gadgets."</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow" style="width: 50px; height: 50px;">V</div>
                        <div>
                            <h6 class="fw-bold m-0 text-dark">Vikram Patel</h6>
                            <span class="text-muted small">Verified Buyer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Contact Details -->
            <div class="col-lg-5">
                <span class="text-primary fw-bold text-uppercase tracking-wider">Get In Touch</span>
                <h2 class="fw-bold mb-4 display-6">We're Here to Help</h2>
                <p class="text-muted mb-5">Have a question about a product or need to check repair status? Drop us a line or visit our store.</p>
                
                <div class="d-flex align-items-start gap-4 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fa-solid fa-location-dot fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Our Store</h5>
                        <p class="text-muted m-0">New narsala road, opposite Dhanashree apartment, Narsala, Nagpur, Maharashtra, 440034.</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-4 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fa-solid fa-phone fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Call Us</h5>
                        <p class="text-muted m-0">8956586537</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start gap-4 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="fa-solid fa-envelope fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Email Us</h5>
                        <p class="text-muted m-0">Altamashmobiles01@gmail.com</p>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-light h-100">
                    <h3 class="fw-bold mb-4">Send us a Message</h3>
                    <form action="#" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control form-control-lg border-0 shadow-sm" placeholder="Enter Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control form-control-lg border-0 shadow-sm" placeholder="Enter Email" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Subject</label>
                                <input type="text" class="form-control form-control-lg border-0 shadow-sm" placeholder="Enter Subject" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Message</label>
                                <textarea class="form-control form-control-lg border-0 shadow-sm" rows="4" placeholder="Enter Message" required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow-sm w-100 hover-scale text-dark">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Carousel Styles */
    .carousel-img-wrapper {
        position: relative;
        height: 80vh;
        min-height: 500px;
        max-height: 800px;
        width: 100%;
    }
    .carousel-img-wrapper img {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    /* Sleek gradient overlays instead of solid flat overlay */
    .carousel-gradient-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.6) 40%, rgba(0,0,0,0) 100%);
        z-index: 1;
    }
    .carousel-item:nth-child(2) .carousel-gradient-overlay {
        background: linear-gradient(270deg, rgba(15,23,42,0.95) 0%, rgba(15,23,42,0.6) 40%, rgba(0,0,0,0) 100%);
    }
    .carousel-gradient-overlay.center-gradient {
        background: radial-gradient(circle, rgba(15,23,42,0.7) 0%, rgba(15,23,42,0.9) 100%);
    }
    .carousel-caption {
        z-index: 2;
        bottom: 0;
        top: 0;
    }
    .carousel-control-prev, .carousel-control-next {
        z-index: 5;
        width: 6%;
    }
    
    /* Animations */
    .animate-fade-right {
        animation: fadeRight 1s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        opacity: 0;
        transform: translateX(-30px);
    }
    .animate-fade-left {
        animation: fadeLeft 1s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        opacity: 0;
        transform: translateX(30px);
    }
    .animate-fade-up {
        animation: fadeUp 1s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        opacity: 0;
        transform: translateY(30px);
    }
    @keyframes fadeRight { to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeLeft { to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    
    /* Utilities */
    .tracking-wider { letter-spacing: 1.5px; }
    .transition-all { transition: all 0.3s ease; }
    .hover-scale { transition: transform 0.2s ease; }
    .hover-scale:hover { transform: scale(1.05); }
    
    /* Category Cards */
    .category-card { border: 1px solid #eee !important; border-radius: 12px; }
    .category-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
        border-color: var(--secondary) !important;
    }
    .category-card:hover .icon-box { transform: scale(1.1); color: var(--secondary) !important; }
    
    /* Product Cards */
    .product-card { border-radius: 12px; overflow: hidden; border: 1px solid #eee !important; }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .product-card:hover .product-img { transform: scale(1.05); }
    .object-fit-contain { object-fit: contain; }
    
    /* Review Cards */
    .review-card { border: 1px solid #f1f5f9 !important; transition: transform 0.3s, box-shadow 0.3s; }
    .review-card:hover { 
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.08)!important;
    }
    
    /* Responsive Typography & Layout */
    .hero-title {
        font-size: 2.5rem;
    }
    @media (min-width: 768px) {
        .hero-title {
            font-size: 4rem;
        }
    }
    @media (min-width: 576px) {
        .w-sm-auto {
            width: auto !important;
        }
    }
    @media (max-width: 767.98px) {
        .carousel-caption {
            padding: 1.5rem !important;
        }
        .carousel-img-wrapper {
            height: 70vh;
            min-height: 480px;
        }
        .hero-title {
            font-size: 2.2rem;
        }
        .btn-lg {
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            font-size: 1.1rem;
        }
    }
</style>
@endpush
@endsection
