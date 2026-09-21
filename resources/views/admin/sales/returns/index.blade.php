@extends('layouts.admin')

@section('title', 'Sales Returns')
@section('page_title', 'Customer Sales Returns')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Sales Returns</h4>
            <small class="text-muted">Manage product returns, refunds, and automatic stock restoration</small>
        </div>
        <a href="{{ route('admin.sales.returns.create') }}" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Process New Return
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Return No</th>
                        <th>Original Invoice</th>
                        <th>Customer</th>
                        <th>Return Date</th>
                        <th>Items Returned</th>
                        <th>Total Amount</th>
                        <th>Refund Method</th>
                        <th>Processed By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                    <tr>
                        <td><span class="fw-bold font-monospace text-dark">{{ $ret->return_no }}</span></td>
                        <td>
                            <a href="{{ route('admin.sales.show', $ret->sale) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $ret->sale->invoice_no }}
                            </a>
                        </td>
                        <td>{{ $ret->customer?->name ?? 'Walk-in' }}</td>
                        <td>{{ $ret->return_date->format('d M Y') }}</td>
                        <td>
                            @foreach($ret->items as $item)
                                <div class="small">
                                    {{ $item->product->name }} &times; {{ $item->quantity }}
                                    @if($item->serial)
                                        <code class="text-muted">({{ $item->serial->imei_1 }})</code>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td class="fw-bold text-danger">₹{{ number_format($ret->total_amount, 2) }}</td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $ret->refund_method }}</span></td>
                        <td><small class="text-muted">{{ $ret->creator?->name ?? 'Staff' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No sales return records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $returns->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
