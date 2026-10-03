@extends('layouts.public')

@section('title', 'Order Success - ALTAMASH MOBILE')

@section('content')
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 p-5 text-center">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow" style="width: 100px; height: 100px;">
                        <i class="fa-solid fa-check fs-1"></i>
                    </div>
                </div>
                
                <h2 class="fw-bold mb-3">Order Placed Successfully!</h2>
                <p class="lead text-muted mb-4">Thank you for your purchase. Your order has been received and is being processed.</p>
                
                <div class="bg-light p-4 rounded-3 mb-4 text-start d-inline-block w-100 text-center">
                    <h5 class="fw-bold mb-2">Order Reference Number:</h5>
                    <div class="fs-4 fw-bold text-primary">{{ session('invoice_number') }}</div>
                    <small class="text-muted">Please save this number for future reference.</small>
                </div>
                
                <div>
                    <a href="{{ route('shop') }}" class="btn btn-primary-custom px-4 py-2 me-2">Continue Shopping</a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


