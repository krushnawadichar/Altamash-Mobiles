@extends('layouts.public')

@section('title', $product->name . ' - ALTAMASH MOBILE')

@section('content')
<div class="bg-light py-4 border-bottom mb-5">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-decoration-none text-muted">Shop</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="row g-0">
            <!-- Product Images -->
            <div class="col-md-6 bg-white p-5 d-flex flex-column align-items-center justify-content-center border-end">
                @if($product->images->count() > 0)
                    @php $mainImage = $product->images->first()->image; @endphp
                    <img id="mainImage" src="{{ str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/' . $mainImage) }}" class="img-fluid mb-4 rounded shadow-sm" alt="{{ $product->name }}" style="max-height: 400px; object-fit: contain;">
                    
                    <div class="d-flex gap-2 mt-auto">
                        @foreach($product->images as $img)
                            <img src="{{ str_starts_with($img->image, 'http') ? $img->image : asset('storage/' . $img->image) }}" class="img-thumbnail thumbnail-img cursor-pointer" alt="Thumbnail" style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;" onclick="document.getElementById('mainImage').src=this.src">
                        @endforeach
                    </div>
                @else
                    <img id="mainImage" src="https://placehold.co/600x600/f8f9fa/a3a3a3?text=No+Image" class="img-fluid mb-4 rounded shadow-sm" alt="No Image Available" style="max-height: 400px; object-fit: contain;">
                @endif
            </div>

            <!-- Product Details -->
            <div class="col-md-6 p-5 bg-light">
                <div class="mb-2">
                    <span class="badge bg-secondary me-2">{{ $product->category->name ?? '' }}</span>
                    <span class="badge bg-dark">{{ $product->brand->name ?? '' }}</span>
                </div>
                
                <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
                <p class="text-muted mb-4">SKU: {{ $product->sku ?? 'N/A' }}</p>
                
                <div class="mb-4">
                    <h2 class="fw-bold text-primary mb-0">₹{{ number_format($product->selling_price, 2) }}</h2>
                    @if($product->mrp > $product->selling_price)
                        <small class="text-muted text-decoration-line-through">MRP: ₹{{ number_format($product->mrp, 2) }}</small>
                        <span class="text-success ms-2 fw-bold">{{ round((($product->mrp - $product->selling_price) / $product->mrp) * 100) }}% OFF</span>
                    @endif
                </div>

                <div class="mb-4">
                    <p>{!! nl2br(e($product->description)) !!}</p>
                </div>

                <hr class="mb-4">

                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <div class="mb-4 d-flex align-items-center gap-3">
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-outline-secondary" type="button" id="btn-minus"><i class="fa-solid fa-minus"></i></button>
                            <input type="text" class="form-control text-center fw-bold" name="quantity" id="qty" value="1" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="btn-plus"><i class="fa-solid fa-plus"></i></button>
                        </div>
                        
                        @if($product->stock_quantity > 0)
                            <button type="submit" class="btn btn-primary-custom btn-lg flex-grow-1"><i class="fa-solid fa-cart-plus me-2"></i> Add to Cart</button>
                        @else
                            <button type="button" class="btn btn-danger btn-lg flex-grow-1" disabled><i class="fa-solid fa-ban me-2"></i> Out of Stock</button>
                        @endif
                    </div>
                </form>
                
                @if($product->stock_quantity > 0 && $product->stock_quantity < 5)
                    <div class="text-danger fw-bold small"><i class="fa-solid fa-fire me-1"></i> Hurry! Only {{ $product->stock_quantity }} left in stock.</div>
                @elseif($product->stock_quantity >= 5)
                    <div class="text-success fw-bold small"><i class="fa-solid fa-check-circle me-1"></i> In Stock</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-5 pt-5 border-top">
        <h3 class="fw-bold mb-4">Related Products</h3>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm product-card">
                    @if($related->images->count() > 0)
                        @php $mainImage = $related->images->first()->image; @endphp
                        <img src="{{ str_starts_with($mainImage, 'http') ? $mainImage : asset('storage/' . $mainImage) }}" class="card-img-top p-3" alt="{{ $related->name }}" style="height: 180px; object-fit: contain;">
                    @else
                        <img src="https://placehold.co/600x600/f8f9fa/a3a3a3?text=No+Image" class="card-img-top p-3" alt="No Image Available" style="height: 180px; object-fit: contain;">
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-truncate mb-2">
                            <a href="{{ route('product.show', $related->slug) }}" class="text-dark text-decoration-none stretched-link">{{ $related->name }}</a>
                        </h6>
                        <div class="mt-auto fw-bold text-primary">₹{{ number_format($related->selling_price, 2) }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#btn-plus').click(function() {
            var val = parseInt($('#qty').val());
            var max = {{ $product->stock_quantity }};
            if(val < max) $('#qty').val(val + 1);
        });
        $('#btn-minus').click(function() {
            var val = parseInt($('#qty').val());
            if(val > 1) $('#qty').val(val - 1);
        });
    });
</script>
@endpush
@endsection


