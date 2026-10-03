@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Sales / Orders</h2>
        <p class="text-muted">Manage all customer sales and invoices</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.pos.index') }}" class="btn btn-primary"><i class="fa-solid fa-cash-register me-2"></i> POS (New Sale)</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">All Sales</h5>
        <form action="{{ route('admin.sales.index') }}" method="GET" class="d-flex" style="max-width: 350px;">
            <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search Invoice or Customer..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-search"></i></button>
            @if(request('search'))
                <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-outline-secondary ms-1" title="Clear Search"><i class="fa-solid fa-times"></i></a>
            @endif
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                        <td class="fw-bold">{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                        <td class="fw-bold text-primary">₹{{ number_format($sale->grand_total, 2) }}</td>
                        <td class="text-success">₹{{ number_format($sale->paid_amount, 2) }}</td>
                        <td>
                            @if($sale->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($sale->payment_status == 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye"></i> View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection


