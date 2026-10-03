@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="text-muted">Welcome to the ALTAMASH MOBILE management system.</p>
    </div>
</div>

<!-- Placeholder for stats cards to be implemented later -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="fa-solid fa-box fs-1 opacity-50"></i>
                </div>
                <div>
                    <h6 class="card-title text-uppercase opacity-75 mb-1">Total Products</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalProducts) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-success text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="fa-solid fa-indian-rupee-sign fs-1 opacity-50"></i>
                </div>
                <div>
                    <h6 class="card-title text-uppercase opacity-75 mb-1">Today's Sales</h6>
                    <h3 class="mb-0 fw-bold">₹{{ number_format($todaysSales, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-info text-white h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="fa-solid fa-cart-shopping fs-1 opacity-50"></i>
                </div>
                <div>
                    <h6 class="card-title text-uppercase opacity-75 mb-1">Total Orders</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalOrders) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 bg-warning text-dark h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <i class="fa-solid fa-screwdriver-wrench fs-1 opacity-50"></i>
                </div>
                <div>
                    <h6 class="card-title text-uppercase opacity-75 mb-1">Pending Repairs</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($pendingRepairs) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Recent Sales</h5>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-outline-primary">View All Sales</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table datatable table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th class="pe-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSales as $sale)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $sale->invoice_number }}</td>
                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td>{{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</td>
                                    <td class="fw-bold">₹{{ number_format($sale->grand_total, 2) }}</td>
                                    <td>
                                        @if($sale->payment_status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($sale->payment_status == 'partial')
                                            <span class="badge bg-warning text-dark">Partial</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $sale->payment_method }}</span></td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-outline-primary" title="View Sale"><i class="fa-solid fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


