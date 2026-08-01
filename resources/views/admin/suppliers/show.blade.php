@extends('layouts.app')

@section('title', $supplier->name . ' - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-truck me-2 text-primary"></i> Supplier Details
        </h2>
        <div>
            <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
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
                            <td>{{ $supplier->name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Company</td>
                            <td>{{ $supplier->company_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Email</td>
                            <td>{{ $supplier->email }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone</td>
                            <td>{{ $supplier->phone }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Alternative Phone</td>
                            <td>{{ $supplier->alternative_phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Address</td>
                            <td>{{ $supplier->address }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">GST Number</td>
                            <td>{{ $supplier->gst_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">PAN Number</td>
                            <td>{{ $supplier->pan_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Opening Balance</td>
                            <td>Rs. {{ number_format($supplier->opening_balance, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Current Balance</td>
                            <td class="fw-bold {{ $supplier->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format($supplier->current_balance, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Notes</td>
                            <td>{{ $supplier->notes ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($supplier->purchases->count() > 0)
                <div class="mt-4">
                    <h5 class="fw-bold">Recent Purchases</h5>
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
                                @foreach($supplier->purchases->take(5) as $purchase)
                                    <tr>
                                        <td>{{ $purchase->invoice_number }}</td>
                                        <td>{{ $purchase->purchase_date->format('d M, Y') }}</td>
                                        <td>Rs. {{ number_format($purchase->total_amount, 2) }}</td>
                                        <td>
                                            @if($purchase->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($purchase->payment_status == 'partial')
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