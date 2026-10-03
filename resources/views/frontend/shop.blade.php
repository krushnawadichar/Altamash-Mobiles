@extends('layouts.public')

@section('content')
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <h1 class="fw-bold mb-0">Shop</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shop</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4 mb-lg-0">
            <div class="position-sticky" style="top: 100px; z-index: 10;">
                <form action="{{ route('shop') }}" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search products..." value="{{ request('q') }}">
                        <input type="hidden" name="category" value="{{ request('category') }}">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-search"></i></button>
                    </div>
                </form>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Categories</h5>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('shop', array_merge(request()->query(), ['category' => null])) }}" class="text-decoration-none {{ !request('category') ? 'text-primary fw-bold' : 'text-muted' }}">
                                All Categories
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('shop', array_merge(request()->query(), ['category' => $category->id])) }}" class="text-decoration-none {{ request('category') == $category->id ? 'text-primary fw-bold' : 'text-muted' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Promotional/Info Banner (Optional extra design element) -->
                <div class="card border-0 bg-primary text-white rounded-4 shadow-sm overflow-hidden d-none d-lg-block">
                    <div class="card-body p-4 text-center">
                        <i class="fa-solid fa-headset fs-1 mb-3 text-warning"></i>
                        <h5 class="fw-bold">Need Help?</h5>
                        <p class="small mb-3 text-white-50">Our experts are ready to assist you in choosing the perfect device.</p>
                        <a href="{{ route('contact') }}" class="btn btn-warning btn-sm fw-bold rounded-pill w-100 text-dark">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted m-0">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</p>
                <form action="{{ route('shop') }}" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <label class="me-2 text-nowrap fw-bold text-muted">Sort By:</label>
                    <select name="sort" class="form-select form-select-sm border-0 shadow-sm rounded-3" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </form>
            </div>

            <div class="row g-4 mb-4">
                @forelse($products as $product)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        @if($product->images->count() > 0)
                            @php $mainImage = $product->images->first()->image; @endphp
                            <img src="{{ str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/' . $mainImage) }}" class="card-img-top p-3" alt="{{ $product->name }}" style="height: 200px; object-fit: contain;">
                        @else
                            <img src="https://placehold.co/600x600/f8f9fa/a3a3a3?text=No+Image" class="card-img-top p-3" alt="No Image Available" style="height: 200px; object-fit: contain;">
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted mb-1">{{ $product->category->name ?? 'Uncategorized' }}</small>
                            <h6 class="card-title text-truncate mb-2">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-dark text-decoration-none stretched-link">{{ $product->name }}</a>
                            </h6>
                            
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="fw-bold fs-5 text-primary">₹{{ number_format($product->selling_price, 2) }}</div>
                                <button class="btn btn-sm btn-outline-primary" style="z-index: 2; position: relative;"><i class="fa-solid fa-cart-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-box-open text-muted fs-1 mb-3"></i>
                    <h4 class="text-muted">No products found.</h4>
                    <p>Try selecting a different category.</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .pagination svg {
        width: 1.25rem;
        height: 1.25rem;
    }
    .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    .page-link {
        color: var(--primary);
    }
</style>
@endpush
@endsection


