@extends('layouts.admin')

@section('title', 'Suppliers Management')
@section('page_title', 'Supplier Directory & Ledger')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Suppliers</h4>
            <small class="text-muted">Manage mobile distributors, accessory wholesalers, and parts vendors</small>
        </div>
        <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
            <i class="bi bi-plus-lg me-1"></i> Add Supplier
        </button>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.suppliers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search supplier name, company, or mobile..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch pt-1">
                        <input class="form-check-input" type="checkbox" name="has_due" value="1" id="hasDueCheck" {{ request('has_due') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label small fw-semibold" for="hasDueCheck">Only Suppliers with Outstanding Dues</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Search</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Supplier Name</th>
                        <th>Company</th>
                        <th>Contact Number</th>
                        <th>GSTIN</th>
                        <th>Total Purchases</th>
                        <th>Outstanding Due</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>
                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $supplier->name }}
                            </a>
                        </td>
                        <td>{{ $supplier->company_name ?: '-' }}</td>
                        <td>
                            <a href="tel:{{ $supplier->mobile }}" class="text-decoration-none text-secondary">
                                <i class="bi bi-telephone me-1"></i>{{ $supplier->mobile }}
                            </a>
                        </td>
                        <td><code>{{ $supplier->gst_number ?: '-' }}</code></td>
                        <td><span class="badge bg-light text-dark border">{{ $supplier->purchases_count }} Orders</span></td>
                        <td>
                            <span class="fw-bold {{ $supplier->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format($supplier->current_balance, 2) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-success py-0 px-2" data-bs-toggle="modal" data-bs-target="#payModal{{ $supplier->id }}" {{ $supplier->current_balance <= 0 ? 'disabled' : '' }}>
                                <i class="bi bi-cash-stack"></i> Pay Due
                            </button>
                            <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-sm btn-light border py-0 px-2">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Payment Modal -->
                    <div class="modal fade" id="payModal{{ $supplier->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.suppliers.payment', $supplier) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Pay Supplier: {{ $supplier->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning py-2 mb-3 small">
                                            Current Payable Due: <strong>₹{{ number_format($supplier->current_balance, 2) }}</strong>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Payment Amount (₹) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="amount" class="form-control fw-bold" max="{{ $supplier->current_balance }}" value="{{ $supplier->current_balance }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                                            <select name="payment_method" class="form-select" required>
                                                <option value="bank_transfer" selected>Bank Transfer (NEFT/RTGS/IMPS)</option>
                                                <option value="upi">UPI (GPay / PhonePe / Paytm)</option>
                                                <option value="cash">Cash</option>
                                                <option value="cheque">Cheque</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Reference / UTR / Cheque Number</label>
                                            <input type="text" name="reference_no" class="form-control" placeholder="Transaction reference">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold small">Notes</label>
                                            <input type="text" name="notes" class="form-control" placeholder="Payment notes...">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success fw-bold">Record Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No suppliers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Supplier Contact Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Company / Distributor Name</label>
                            <input type="text" name="company_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">GSTIN Number</label>
                            <input type="text" name="gst_number" class="form-control" placeholder="e.g. 27AAAAA0000A1Z5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Opening Payable Balance (₹)</label>
                            <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
