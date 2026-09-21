@extends('layouts.admin')

@section('title', 'Stock Adjustments')
@section('page_title', 'Stock Adjustments & Corrections')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Stock Adjustments</h4>
            <small class="text-muted">Reconcile physical inventory, report damaged, lost, or found stock</small>
        </div>
        <a href="{{ route('admin.inventory.adjustments.create') }}" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-plus-lg me-1"></i> New Stock Adjustment
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Adjustment No</th>
                        <th>Date</th>
                        <th>Reason</th>
                        <th>Adjusted Items</th>
                        <th>Logged By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                    <tr>
                        <td><span class="fw-bold text-dark font-monospace">{{ $adj->adjustment_no }}</span></td>
                        <td>{{ $adj->date->format('d M Y') }}</td>
                        <td>
                            @php
                                $badgeColor = match($adj->reason) {
                                    'damaged' => 'bg-danger',
                                    'lost' => 'bg-warning text-dark',
                                    'found' => 'bg-success',
                                    'correction' => 'bg-info text-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }}">{{ ucfirst($adj->reason) }}</span>
                        </td>
                        <td>
                            @foreach($adj->items as $item)
                                <div class="small">
                                    <span class="fw-semibold">{{ $item->product->name }}</span>:
                                    <span class="{{ $item->type === 'increase' ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $item->type === 'increase' ? '+' : '-' }}{{ $item->quantity }}
                                    </span>
                                </div>
                            @endforeach
                        </td>
                        <td>{{ $adj->creator?->name ?? 'System' }}</td>
                        <td class="text-muted small">{{ $adj->notes ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No stock adjustments recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $adjustments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
