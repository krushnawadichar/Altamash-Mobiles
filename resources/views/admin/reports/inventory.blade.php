@extends('layouts.admin')

@section('title', 'Inventory Valuation Report')
@section('page_title', 'Stock Valuation & Inventory Report')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Inventory Valuation & Stock Report</h4>
            <small class="text-muted">Total showroom inventory worth at purchase cost vs retail selling price</small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm no-print">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Valuation Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Inventory Items</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalItems }}</h3>
                <small class="text-muted">{{ $totalStockQty }} total units in stock</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Stock Value (at Cost Price)</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">₹{{ number_format($stockValueCost, 2) }}</h3>
                <small class="text-muted">Total capital invested in inventory</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Stock Value (at Retail Price)</div>
                <h3 class="fw-bold text-success mb-0 mt-1">₹{{ number_format($stockValueSelling, 2) }}</h3>
                <small class="text-muted">Total sales value of available stock</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Potential Gross Profit in Stock</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">₹{{ number_format($potentialProfit, 2) }}</h3>
                <small class="text-muted">Expected earnings on 100% sell-through</small>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="bi bi-boxes me-1 text-primary"></i>Stock Valuations by Product</span>
            <span class="badge bg-light text-dark border">{{ $products->count() }} Products</span>
        </div>
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Product & SKU</th>
                        <th>Type</th>
                        <th>Units in Stock</th>
                        <th>Cost / Unit</th>
                        <th>Selling / Unit</th>
                        <th>Total Cost Value</th>
                        <th>Total Retail Value</th>
                        <th>Stock Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->name }}</div>
                            <small class="text-muted">SKU: {{ $p->sku }} &bull; {{ $p->brand?->name ?? '-' }}</small>
                        </td>
                        <td><span class="badge bg-light text-dark border text-capitalize">{{ str_replace('_', ' ', $p->type) }}</span></td>
                        <td><span class="fw-bold fs-6">{{ $p->current_stock }}</span></td>
                        <td>₹{{ number_format($p->purchase_price, 2) }}</td>
                        <td class="text-success fw-semibold">₹{{ number_format($p->selling_price, 2) }}</td>
                        <td class="fw-bold">₹{{ number_format($p->purchase_price * $p->current_stock, 2) }}</td>
                        <td class="fw-bold text-success">₹{{ number_format($p->selling_price * $p->current_stock, 2) }}</td>
                        <td><span class="badge {{ $p->stock_badge_class }}">{{ $p->stock_status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
