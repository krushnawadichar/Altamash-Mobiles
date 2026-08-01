@extends('layouts.app')

@section('title', 'Sales - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-shopping-cart me-2 text-primary"></i> Sales
        </h2>
        <a href="{{ route('admin.sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Sale
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sale->invoice_number }}</td>
                                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                                <td>Rs. {{ number_format($sale->total_amount, 0) }}</td>
                                <td>Rs. {{ number_format($sale->paid_amount, 0) }}</td>
                                <td>Rs. {{ number_format($sale->due_amount, 0) }}</td>
                                <td>
                                    @if($sale->payment_status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($sale->payment_status == 'partial')
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-danger">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.sales.show', $sale->id) }}" 
                                           class="btn btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sales.print', $sale->id) }}" 
                                           class="btn btn-secondary" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('admin.sales.pdf', $sale->id) }}" 
                                           class="btn btn-danger">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('admin.sales.destroy', $sale->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No sales found.</p>
                                    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Sale
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection