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
                        <th>Pending Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                        <td class="fw-bold">{{ $sale->invoice_number }}</td>
                        <td>
                            @if($sale->customer)
                                <a href="{{ route('admin.customers.show', $sale->customer) }}" class="text-decoration-none fw-semibold">
                                    {{ $sale->customer->name }}
                                </a>
                            @else
                                <span class="text-muted">Walk-in Customer</span>
                            @endif
                        </td>
                        <td class="fw-bold text-primary">₹{{ number_format($sale->grand_total, 2) }}</td>
                        <td class="text-success fw-semibold">₹{{ number_format($sale->paid_amount, 2) }}</td>
                        <td class="{{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            ₹{{ number_format($sale->due_amount, 2) }}
                        </td>
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
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-outline-info" title="View Invoice">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-success text-white" 
                                        title="Send Invoice on WhatsApp"
                                        onclick="openWhatsAppModal('{{ $sale->invoice_number }}', '{{ addslashes($sale->customer->name ?? 'Walk-in Customer') }}', '{{ $sale->customer->phone ?? '' }}', '{{ number_format($sale->grand_total, 2) }}', '{{ number_format($sale->paid_amount, 2) }}', '{{ number_format($sale->due_amount, 2) }}', '{{ route('invoice.public', $sale->invoice_number) }}')">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </button>
                                <a href="{{ route('admin.sales.print', $sale) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print Invoice">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection


