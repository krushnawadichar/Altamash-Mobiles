@extends('layouts.admin')

@section('title', 'Stock Ledger & Movement History')
@section('page_title', 'Universal Stock Ledger')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Inventory Stock Ledger</h4>
            <small class="text-muted">Audit trail of all stock movements (Purchases, POS Sales, Repairs, Adjustments)</small>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.inventory.transactions') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Movement Types</option>
                        <option value="PURCHASE" {{ request('type') == 'PURCHASE' ? 'selected' : '' }}>PURCHASE (+ IN)</option>
                        <option value="SALE" {{ request('type') == 'SALE' ? 'selected' : '' }}>SALE (- OUT)</option>
                        <option value="SALE_RETURN" {{ request('type') == 'SALE_RETURN' ? 'selected' : '' }}>SALE RETURN (+ IN)</option>
                        <option value="PURCHASE_RETURN" {{ request('type') == 'PURCHASE_RETURN' ? 'selected' : '' }}>PURCHASE RETURN (- OUT)</option>
                        <option value="REPAIR_PART_USED" {{ request('type') == 'REPAIR_PART_USED' ? 'selected' : '' }}>REPAIR PART USED (- OUT)</option>
                        <option value="STOCK_ADJUSTMENT_IN" {{ request('type') == 'STOCK_ADJUSTMENT_IN' ? 'selected' : '' }}>ADJUSTMENT (+ IN)</option>
                        <option value="STOCK_ADJUSTMENT_OUT" {{ request('type') == 'STOCK_ADJUSTMENT_OUT' ? 'selected' : '' }}>ADJUSTMENT (- OUT)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" onchange="this.form.submit()">
                </div>

                <div class="col-md-2">
                    <a href="{{ route('admin.inventory.transactions') }}" class="btn btn-sm btn-light border w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Product</th>
                        <th>IMEI / Serial</th>
                        <th>Movement Type</th>
                        <th>Qty Change</th>
                        <th>Before &rarr; After</th>
                        <th>Unit Cost</th>
                        <th>Logged By</th>
                        <th>Notes / Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td class="text-muted small">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <a href="{{ route('admin.products.show', $tx->product) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $tx->product->name }}
                            </a>
                            <div class="text-muted small">SKU: <code>{{ $tx->product->sku }}</code></div>
                        </td>
                        <td>
                            @if($tx->serial)
                                <code class="text-dark bg-light px-1 py-0.5 border rounded">{{ $tx->serial->imei_1 }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = match($tx->transaction_type) {
                                    'PURCHASE' => 'bg-success-subtle text-success border border-success-subtle',
                                    'SALE' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    'SALE_RETURN' => 'bg-info-subtle text-info border border-info-subtle',
                                    'REPAIR_PART_USED' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                    'STOCK_ADJUSTMENT_IN' => 'bg-success-subtle text-success',
                                    'STOCK_ADJUSTMENT_OUT' => 'bg-danger-subtle text-danger',
                                    default => 'bg-light text-dark'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $tx->transaction_type }}</span>
                        </td>
                        <td>
                            <span class="fw-bold fs-6 {{ $tx->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $tx->quantity > 0 ? '+' . $tx->quantity : $tx->quantity }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $tx->before_stock }}</span>
                            <i class="bi bi-arrow-right mx-1 text-muted small"></i>
                            <span class="fw-bold text-dark">{{ $tx->after_stock }}</span>
                        </td>
                        <td>₹{{ number_format($tx->unit_cost, 2) }}</td>
                        <td><small class="text-muted">{{ $tx->user?->name ?? 'System' }}</small></td>
                        <td class="text-muted small">{{ $tx->notes ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No stock transactions logged yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
