@extends('layouts.admin')

@section('title', 'Smartphone Stock & IMEI Report')
@section('page_title', 'Mobile Phone Stock Report')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Smartphones & IMEI Inventory Report</h4>
            <small class="text-muted">Breakdown of mobile stock, available IMEIs in showroom, and sold devices</small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm no-print">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Smartphone Models</div>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $mobileProducts->count() }}</h3>
                <small class="text-muted">Unique phone variants</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Available IMEIs (In Stock)</div>
                <h3 class="fw-bold text-success mb-0 mt-1">{{ $availableImeisCount }}</h3>
                <small class="text-muted">Ready for retail billing</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Sold Mobile Phones</div>
                <h3 class="fw-bold text-secondary mb-0 mt-1">{{ $soldImeisCount }}</h3>
                <small class="text-muted">Historical sold devices</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Capital in Mobile Stock</div>
                <h3 class="fw-bold text-primary mb-0 mt-1">₹{{ number_format($mobileStockValue, 2) }}</h3>
                <small class="text-muted">Purchase cost of available units</small>
            </div>
        </div>
    </div>

    <!-- Mobile Models Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <span class="fw-bold"><i class="bi bi-phone me-1 text-primary"></i>Smartphone Models & Stock Detail</span>
        </div>
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Model Name</th>
                        <th>Brand</th>
                        <th>SKU</th>
                        <th>Available Stock</th>
                        <th>Available IMEIs</th>
                        <th>Purchase Cost</th>
                        <th>Selling Price</th>
                        <th>Total Stock Worth</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mobileProducts as $m)
                    <tr>
                        <td class="fw-bold text-dark">{{ $m->name }}</td>
                        <td>{{ $m->brand?->name ?? '-' }}</td>
                        <td><code>{{ $m->sku }}</code></td>
                        <td>
                            <span class="badge {{ $m->stock_badge_class }} fs-7">
                                {{ $m->current_stock }} units
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($m->availableSerials->take(3) as $s)
                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">{{ $s->imei_1 }}</span>
                                @empty
                                    <span class="text-muted small">No IMEIs</span>
                                @endforelse
                                @if($m->availableSerials->count() > 3)
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.72rem;">+{{ $m->availableSerials->count() - 3 }} more</span>
                                @endif
                            </div>
                        </td>
                        <td>₹{{ number_format($m->purchase_price, 2) }}</td>
                        <td class="text-success fw-bold">₹{{ number_format($m->selling_price, 2) }}</td>
                        <td class="fw-bold text-primary">₹{{ number_format($m->purchase_price * $m->current_stock, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No smartphone products registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
