@extends('layouts.app')

@section('title', 'Barcode - ' . $product->name)

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-barcode me-2 text-primary"></i> Barcode
        </h2>
        <div>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="barcode-wrapper d-inline-block p-4 border">
                <h4 class="mb-3">{{ $product->name }}</h4>
                <div>
                    {!! DNS1D::getBarcodeHTML($product->barcode, 'C128', 2, 80) !!}
                </div>
                <p class="mt-3 mb-0">
                    <strong>SKU:</strong> {{ $product->sku }}<br>
                    <strong>Price:</strong> Rs. {{ number_format($product->selling_price, 0) }}
                </p>
                <p class="mt-2 text-muted small">{{ $product->barcode }}</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .navbar, #sidebar-wrapper, footer {
        display: none !important;
    }
    #page-content-wrapper {
        margin-left: 0 !important;
    }
    .barcode-wrapper {
        border: none !important;
        margin: 0 auto;
    }
}
</style>
@endsection