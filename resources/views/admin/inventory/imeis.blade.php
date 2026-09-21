@extends('layouts.admin')

@section('title', 'IMEI & Serial Number Tracker')
@section('page_title', 'IMEI Tracking Directory')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Smartphone IMEI Tracker</h4>
            <small class="text-muted">Track individual mobile phone devices by IMEI1, IMEI2, and Serial Number</small>
        </div>
        <div>
            <span class="badge bg-success p-2">Available: {{ \App\Models\ProductSerial::where('status', 'available')->count() }}</span>
            <span class="badge bg-secondary p-2 ms-1">Sold: {{ \App\Models\ProductSerial::where('status', 'sold')->count() }}</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.inventory.imeis') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search IMEI 1, IMEI 2, Serial, or Phone Name..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available for Sale</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="under_repair" {{ request('status') == 'under_repair' ? 'selected' : '' }}>Under Repair</option>
                        <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary px-3">Search</button>
                    <a href="{{ route('admin.inventory.imeis') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- IMEI Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Smartphone Device</th>
                        <th>IMEI 1 (Primary)</th>
                        <th>IMEI 2</th>
                        <th>Serial No</th>
                        <th>Specs / Color</th>
                        <th>Purchase Cost</th>
                        <th>Status</th>
                        <th>Sale Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serials as $serial)
                    <tr>
                        <td>
                            <a href="{{ route('admin.products.show', $serial->product) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $serial->product->name }}
                            </a>
                            <div class="text-muted small">Brand: {{ $serial->product->brand?->name ?? '-' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace fs-7 px-2 py-1">
                                {{ $serial->imei_1 }}
                            </span>
                        </td>
                        <td>
                            <span class="font-monospace text-muted small">{{ $serial->imei_2 ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="font-monospace text-muted small">{{ $serial->serial_no ?: '-' }}</span>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $serial->color ?: 'Standard' }}</div>
                            <small class="text-muted">{{ $serial->ram ? $serial->ram . ' RAM' : '' }} {{ $serial->storage ? '/ ' . $serial->storage : '' }}</small>
                        </td>
                        <td class="text-secondary">₹{{ number_format($serial->purchase_price, 2) }}</td>
                        <td>
                            <span class="badge {{ $serial->status_badge_class }}">
                                {{ ucfirst(str_replace('_', ' ', $serial->status)) }}
                            </span>
                        </td>
                        <td>
                            @if($serial->isSold() && $serial->saleItem?->sale)
                                <div>
                                    <a href="{{ route('admin.sales.show', $serial->saleItem->sale) }}" class="fw-bold text-primary text-decoration-none small">
                                        {{ $serial->saleItem->sale->invoice_no }}
                                    </a>
                                </div>
                                <small class="text-muted">{{ $serial->sold_at ? $serial->sold_at->format('d M Y') : '' }}</small>
                            @else
                                <span class="text-success small fw-semibold"><i class="bi bi-check2 me-1"></i>In Showroom</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No IMEI or serial records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($serials->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $serials->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
