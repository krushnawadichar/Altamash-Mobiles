@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Purchase Details</h2>
        <a href="{{ route('admin.purchases.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Purchases</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold">Supplier Info</h5>
                <hr>
                <p class="mb-1"><strong>Name:</strong> {{ $purchase->supplier->name }}</p>
                <p class="mb-1"><strong>Company:</strong> {{ $purchase->supplier->company_name }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $purchase->supplier->phone }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $purchase->supplier->email }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold">Purchase Info</h5>
                <hr>
                <p class="mb-1"><strong>Date:</strong> {{ $purchase->purchase_date->format('d M Y') }}</p>
                <p class="mb-1"><strong>Invoice No:</strong> {{ $purchase->invoice_number ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Created By:</strong> {{ $purchase->creator->name ?? 'System' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body text-end">
                <h5 class="card-title fw-bold text-start">Payment Status</h5>
                <hr>
                <h3 class="mb-0 text-primary">₹{{ number_format($purchase->grand_total, 2) }}</h3>
                <p class="mb-1 text-muted">Grand Total</p>
                <p class="mb-1 text-success">Paid: ₹{{ number_format($purchase->paid_amount, 2) }}</p>
                <p class="mb-1 text-danger">Due: ₹{{ number_format($purchase->due_amount, 2) }}</p>
                <div class="mt-2 text-start">
                    Status: 
                    @if($purchase->payment_status == 'paid')
                        <span class="badge bg-success fs-6">Paid</span>
                    @elseif($purchase->payment_status == 'partial')
                        <span class="badge bg-warning text-dark fs-6">Partial</span>
                    @else
                        <span class="badge bg-danger fs-6">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Items Purchased</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-end">₹{{ number_format($item->purchase_price, 2) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Subtotal</td>
                        <td class="text-end">₹{{ number_format($purchase->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Discount</td>
                        <td class="text-end text-danger">- ₹{{ number_format($purchase->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Tax</td>
                        <td class="text-end">+ ₹{{ number_format($purchase->tax, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold fs-5">Grand Total</td>
                        <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection


