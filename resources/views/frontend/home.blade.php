@extends('layouts.public')

@section('content')
<!-- Hero Section -->
<div class="bg-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Next-Gen Electronics at Unbeatable Prices</h1>
                <p class="lead mb-4">Discover the latest smartphones, laptops, home appliances, and accessories with exclusive deals and premium support.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('shop') }}" class="btn btn-secondary-custom btn-lg px-4">Shop Now</a>
                    <a href="#categories" class="btn btn-outline-light btn-lg px-4">Browse Categories</a>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                <!-- A placeholder hero image - normally you'd use a real banner image -->
                <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Electronics" class="img-fluid rounded-4 shadow-lg" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</div>

<!-- Features -->
<div class="py-5 bg-white border-bottom">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-3">
                <i class="fa-solid fa-truck-fast text-primary fs-1 mb-3"></i>
                <h5>Free Delivery</h5>
                <p class="text-muted small">On orders over ₹10,000</p>
            </div>
            <div class="col-md-3">
                <i class="fa-solid fa-shield-halved text-primary fs-1 mb-3"></i>
                <h5>1 Year Warranty</h5>
                <p class="text-muted small">On all electronic devices</p>
            </div>
            <div class="col-md-3">
                <i class="fa-solid fa-screwdriver-wrench text-primary fs-1 mb-3"></i>
                <h5>Expert Repair</h5>
                <p class="text-muted small">Quick and reliable service</p>
            </div>
            <div class="col-md-3">
                <i class="fa-solid fa-headset text-primary fs-1 mb-3"></i>
                <h5>24/7 Support</h5>
                <p class="text-muted small">Always here to help you</p>
            </div>
        </div>
    </div>
</div>

<!-- Shop by Category -->
<div id="categories" class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h2 class="fw-bold m-0">Shop by Category</h2>
            <a href="{{ route('shop') }}" class="text-primary text-decoration-none fw-bold">View All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        
        <div class="row g-4">
            @foreach($categories as $category)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('shop', ['category' => $category->id]) }}" class="text-decoration-none">
                    <div class="card h-100 text-center border-0 shadow-sm category-card transition-all">
                        <div class="card-body py-4">
                            <!-- Placeholder icon based on category name -->
                            @php
                                $icon = 'fa-box';
                                if(str_contains(strtolower($category->name), 'mobile') || str_contains(strtolower($category->name), 'phone')) $icon = 'fa-mobile-screen-button';
                                elseif(str_contains(strtolower($category->name), 'laptop') || str_contains(strtolower($category->name), 'computer')) $icon = 'fa-laptop';
                                elseif(str_contains(strtolower($category->name), 'tv') || str_contains(strtolower($category->name), 'television')) $icon = 'fa-tv';
                                elseif(str_contains(strtolower($category->name), 'audio') || str_contains(strtolower($category->name), 'headphone')) $icon = 'fa-headphones';
                                elseif(str_contains(strtolower($category->name), 'watch')) $icon = 'fa-clock';
                                elseif(str_contains(strtolower($category->name), 'camera')) $icon = 'fa-camera';
                            @endphp
                            <i class="fa-solid {{ $icon }} fs-1 text-primary mb-3"></i>
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
    <div class="container">
        <h2 class="fw-bold mb-4 text-center">Featured Products</h2>
        
        <div class="row g-4">
            @forelse($featuredProducts as $product)
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm product-card">
                    @if($product->images->count() > 0)
                        @php $mainImage = $product->images->first()->image; @endphp
                        <img src="{{ str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/' . $mainImage) }}" class="card-img-top p-3" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center p-3" style="height: 200px;">
                            <i class="fa-solid fa-image text-muted fs-1"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-1">{{ $product->category->name ?? 'Uncategorized' }}</small>
                        <h5 class="card-title text-truncate">
                            <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none stretched-link">{{ $product->name }}</a>
                        </h5>
                        
                        <div class="mt-auto pt-3 d-flex justify-content-between align-items-center">
                            <div class="fw-bold fs-5 text-primary">₹{{ number_format($product->selling_price, 2) }}</div>
                            <button class="btn btn-sm btn-outline-primary" style="z-index: 2; position: relative;"><i class="fa-solid fa-cart-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">No featured products available at the moment.</p>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('shop') }}" class="btn btn-primary-custom px-5 py-2">Explore All Products</a>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-5 text-center bg-dark text-white">
    <div class="container py-4">
        <h2 class="fw-bold mb-3">Need Your Device Repaired?</h2>
        <p class="lead mb-4 text-white-50">Our expert technicians provide fast and reliable repair services for smartphones, laptops, and more.</p>
        <a href="{{ route('contact') }}" class="btn btn-secondary-custom btn-lg px-5">Contact Support</a>
    </div>
</div>

@push('styles')
<style>
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endpush
@endsection


