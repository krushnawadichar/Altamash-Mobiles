@extends('layouts.public')

@section('title', 'Shopping Cart - ALTAMASH MOBILE')

@section('content')
<div class="bg-light py-4 border-bottom mb-5">
    <div class="container">
        <h1 class="fw-bold mb-0">Shopping Cart</h1>
    </div>
</div>

<div class="container mb-5 pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($cart) > 0)
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Product</th>
                                        <th>Price</th>
                                        <th style="width: 150px;">Quantity</th>
                                        <th>Subtotal</th>
                                        <th class="pe-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $details)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($details['image'])
                                                    <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="fa-solid fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <h6 class="mb-0 fw-bold">{{ $details['name'] }}</h6>
                                            </div>
                                        </td>
                                        <td>₹{{ number_format($details['price'], 2) }}</td>
                                        <td>
                                            <form action="{{ route('cart.update') }}" method="POST" class="d-flex align-items-center m-0">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <input type="number" name="quantity" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center w-50" min="1" onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="fw-bold">₹{{ number_format($details['price'] * $details['quantity'], 2) }}</td>
                                        <td class="pe-4 text-end">
                                            <form action="{{ route('cart.remove') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-light">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold text-success">Free</span>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="fw-bold mb-0">Total</h5>
                            <h5 class="fw-bold text-primary mb-0">₹{{ number_format($total, 2) }}</h5>
                        </div>

                        <a href="{{ route('checkout') }}" class="btn btn-primary-custom w-100 py-3 fw-bold fs-5 mb-3">Proceed to Checkout</a>
                        <a href="{{ route('shop') }}" class="btn btn-outline-secondary w-100 py-2">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fa-solid fa-cart-shopping text-muted mb-4" style="font-size: 5rem;"></i>
            <h2 class="fw-bold mb-3">Your cart is empty</h2>
            <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('shop') }}" class="btn btn-primary-custom px-5 py-2">Start Shopping</a>
        </div>
    @endif
</div>
@endsection


