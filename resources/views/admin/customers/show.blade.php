@extends('layouts.app')

@section('title', $customer->name . ' - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-user-friends me-2 text-primary"></i> Customer Details
        </h2>
        <div>
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Name</td>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email</td>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone</td>
                            <td>{{ $customer->phone }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Alternative Phone</td>
                            <td>{{ $customer->alternative_phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Address</td>
                            <td>{{ $customer->address ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">GST Number</td>
                            <td>{{ $customer->gst_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">PAN Number</td>
                            <td>{{ $customer->pan_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Purchases</td>
                            <td>{{ $customer->total_purchases }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Purchase Amount</td>
                            <td>Rs. {{ number_format($customer->total_purchase_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Balance</td>
                            <td class="fw-bold {{ $customer->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format($customer->current_balance, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status</td>
                            <td>
                                @if($customer->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($customer->sales->count() > 0)
                <div class="mt-4">
                    <h5 class="fw-bold">Recent Sales</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->sales->take(5) as $sale)
                                    <tr>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                                        <td>Rs. {{ number_format($sale->total_amount, 2) }}</td>
                                        <td>
                                            @if($sale->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($sale->payment_status == 'partial')
                                                <span class="badge bg-warning">Partial</span>
                                            @else
                                                <span class="badge bg-danger">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection