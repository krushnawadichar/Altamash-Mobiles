@extends('layouts.admin')

@section('title', $product->name)
@section('page_title', 'Product Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $product->name }}</h4>
            <span class="badge {{ $product->stock_badge_class }}">{{ $product->current_stock }} in stock ({{ $product->stock_status }})</span>
            <span class="badge bg-light text-dark border ms-1">SKU: {{ $product->sku }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.barcode', $product) }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-upc-scan me-1"></i> Barcode
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Product Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded mb-3 border p-1" style="max-height: 200px; object-fit: contain;">
                    @else
                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted border mx-auto mb-3" style="width: 140px; height: 140px;">
                            <i class="bi bi-phone fs-1"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                    <p class="text-muted small mb-3">{{ $product->brand?->name ?? 'Generic' }} &bull; {{ $product->category?->name ?? 'General' }}</p>

                    <div class="row g-2 border-top pt-3 text-start">
                        <div class="col-6">
                            <small class="text-muted d-block">Purchase Price</small>
                            <span class="fw-bold">₹{{ number_format($product->purchase_price, 2) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Selling Price</small>
                            <span class="fw-bold text-success">₹{{ number_format($product->selling_price, 2) }}</span>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted d-block">Min Stock Limit</small>
                            <span class="fw-bold">{{ $product->min_stock }} units</span>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted d-block">Tax / GST</small>
                            <span class="fw-bold">{{ $product->tax_percent }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- If Mobile Phone: Show IMEIs Table -->
            @if($product->isMobile())
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-upc-scan me-1 text-primary"></i>Tracked Mobile Units & IMEIs ({{ $product->serials->count() }})</span>
                    <div>
                        <span class="badge bg-success">{{ $product->availableSerials->count() }} Available</span>
                        <span class="badge bg-secondary ms-1">{{ $product->soldSerials->count() }} Sold</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>IMEI 1</th>
                                <th>IMEI 2</th>
                                <th>Serial No</th>
                                <th>Specs / Color</th>
                                <th>Status</th>
                                <th>Sold At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->serials as $serial)
                            <tr>
                                <td class="fw-bold font-monospace">{{ $serial->imei_1 }}</td>
                                <td class="font-monospace text-muted">{{ $serial->imei_2 ?: '-' }}</td>
                                <td class="font-monospace text-muted">{{ $serial->serial_no ?: '-' }}</td>
                                <td>{{ $serial->color }} {{ $serial->ram ? '| ' . $serial->ram : '' }} {{ $serial->storage ? '| ' . $serial->storage : '' }}</td>
                                <td>
                                    <span class="badge {{ $serial->status_badge_class }}">
                                        {{ ucfirst($serial->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $serial->sold_at ? $serial->sold_at->format('d M Y, h:i A') : '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No serial / IMEI records found. Register units via Purchase.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Stock Movement History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-clock-history me-1 text-primary"></i>Stock Movement Ledger</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Stock Before &rarr; After</th>
                                <th>Notes / Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->inventoryTransactions->take(15) as $tx)
                            <tr>
                                <td class="text-muted">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $tx->transaction_type }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $tx->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->quantity > 0 ? '+' . $tx->quantity : $tx->quantity }}
                                    </span>
                                </td>
                                <td>{{ $tx->before_stock }} &rarr; <strong>{{ $tx->after_stock }}</strong></td>
                                <td class="text-muted">{{ $tx->notes ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No stock transactions recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
