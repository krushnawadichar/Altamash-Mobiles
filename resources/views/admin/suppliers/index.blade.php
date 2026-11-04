@extends('layouts.admin')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold mb-1">Suppliers & Vendors</h2>
        <p class="text-muted mb-0">Manage product vendors, supplier accounts, and outstanding due balances</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-cart-shopping me-1"></i> Purchases
        </a>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Add Supplier
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Active Suppliers</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0">{{ count($suppliers) }}</h4>
                <div class="text-muted small mt-1">Registered vendor contacts</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Sourced Value</span>
                    <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                        <i class="fa-solid fa-boxes-stacked fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-success mb-0">₹{{ number_format($totalSupplierPurchases ?? 0, 2) }}</h4>
                <div class="text-muted small mt-1">Cumulative purchase orders</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Outstanding Due</span>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger">
                        <i class="fa-solid fa-hourglass-half fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($totalSupplierDue ?? 0, 2) }}</h4>
                <div class="text-danger small mt-1 fw-medium">Payable to suppliers</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-truck-field text-primary me-2"></i> Supplier Directory</h5>
        <span class="badge bg-light text-dark border">{{ count($suppliers) }} suppliers</span>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Supplier / Vendor</th>
                        <th>Contact Details</th>
                        <th>GST / Tax No</th>
                        <th class="text-end">Total Purchases</th>
                        <th class="text-end">Pending Due</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $supplier->name }}</div>
                            @if(!empty($supplier->company_name))
                                <small class="text-secondary fw-semibold">{{ $supplier->company_name }}</small>
                            @endif
                        </td>
                        <td>
                            @if(!empty($supplier->phone))
                                <div><i class="fa-solid fa-phone me-1 text-muted small"></i>{{ $supplier->phone }}</div>
                            @endif
                            @if(!empty($supplier->email))
                                <small class="text-muted"><i class="fa-solid fa-envelope me-1"></i>{{ $supplier->email }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="fw-mono small text-secondary">{{ $supplier->gst_number ?? 'N/A' }}</span>
                        </td>
                        <td class="text-end fw-semibold">
                            ₹{{ number_format($supplier->total_purchases, 2) }}
                            <div class="small text-muted">{{ $supplier->purchases->count() }} bills</div>
                        </td>
                        <td class="text-end">
                            @if($supplier->total_due > 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 px-2 py-1">
                                    ₹{{ number_format($supplier->total_due, 2) }}
                                </span>
                            @else
                                <span class="text-success small fw-semibold"><i class="fa-solid fa-check me-1"></i>Settled</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($supplier->status)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-outline-primary" title="Edit Supplier">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Delete Supplier"><i class="fa-solid fa-trash"></i></button>
                                </form>
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


