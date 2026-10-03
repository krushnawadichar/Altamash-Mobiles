@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Purchases</h2>
        <p class="text-muted">Manage stock purchases from suppliers</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i> Add Purchase</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Supplier</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->purchase_date->format('d M, Y') }}</td>
                        <td>{{ $purchase->invoice_number ?? 'N/A' }}</td>
                        <td class="fw-bold">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td>₹{{ number_format($purchase->grand_total, 2) }}</td>
                        <td class="text-success">₹{{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="text-danger">₹{{ number_format($purchase->due_amount, 2) }}</td>
                        <td>
                            @if($purchase->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($purchase->payment_status == 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.purchases.show', $purchase) }}" class="btn btn-sm btn-outline-info"><i class="fa-solid fa-eye"></i></a>
                            <!-- We generally don't edit purchases once made to keep inventory history strict, but can allow viewing -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection


