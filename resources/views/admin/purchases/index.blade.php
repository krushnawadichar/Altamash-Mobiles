@extends('layouts.admin')

@section('title', 'Purchase Management')
@section('page_title', 'Stock Purchases & Intake')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Purchases</h4>
            <small class="text-muted">Manage inventory stock intakes and supplier purchase invoices</small>
        </div>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary btn-sm fw-bold">
            <i class="bi bi-cart-plus me-1"></i> New Purchase Order
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.purchases.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search purchase no, supplier invoice..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="supplier_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="payment_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Payment Status</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="due" {{ request('payment_status') == 'due' ? 'selected' : '' }}>Due</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Purchase No</th>
                        <th>Supplier</th>
                        <th>Purchase Date</th>
                        <th>Supplier Invoice</th>
                        <th>Grand Total</th>
                        <th>Paid Amount</th>
                        <th>Due Amount</th>
                        <th>Payment Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td>
                            <a href="{{ route('admin.purchases.show', $purchase) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                {{ $purchase->purchase_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $purchase->supplier->name }}</div>
                            <small class="text-muted">{{ $purchase->supplier->company_name ?: '' }}</small>
                        </td>
                        <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td><code>{{ $purchase->supplier_invoice_no ?: '-' }}</code></td>
                        <td class="fw-bold text-dark">₹{{ number_format($purchase->grand_total, 2) }}</td>
                        <td class="text-success">₹{{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="{{ $purchase->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                            ₹{{ number_format($purchase->due_amount, 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $purchase->payment_badge_class }}">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.purchases.show', $purchase) }}" class="btn btn-sm btn-light border py-0 px-2" title="View Purchase Details">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">No purchase records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
