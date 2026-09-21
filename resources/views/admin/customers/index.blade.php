@extends('layouts.admin')

@section('title', 'Customer Management')
@section('page_title', 'Customers & Balances')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Customers</h4>
            <small class="text-muted">Manage regular clients, credit accounts, and purchase histories</small>
        </div>
        <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
            <i class="bi bi-person-plus me-1"></i> Add Customer
        </button>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search customer name, mobile, or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch pt-1">
                        <input class="form-check-input" type="checkbox" name="has_due" value="1" id="hasDueCust" {{ request('has_due') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label small fw-semibold" for="hasDueCust">Customers with Outstanding Due</label>
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Search</button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Customer Name</th>
                        <th>Mobile Number</th>
                        <th>Address</th>
                        <th>Sales / Orders</th>
                        <th>Repairs</th>
                        <th>Outstanding Due</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $customer->name }}
                            </a>
                            @if($customer->email)
                                <div class="text-muted small">{{ $customer->email }}</div>
                            @endif
                        </td>
                        <td>
                            <a href="tel:{{ $customer->mobile }}" class="text-decoration-none text-secondary">
                                <i class="bi bi-telephone me-1"></i>{{ $customer->mobile }}
                            </a>
                        </td>
                        <td><small class="text-muted">{{ Str::limit($customer->address ?: '-', 35) }}</small></td>
                        <td><span class="badge bg-light text-dark border">{{ $customer->sales_count }} Sales</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $customer->repair_jobs_count }} Repairs</span></td>
                        <td>
                            <span class="fw-bold {{ $customer->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format($customer->current_balance, 2) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($customer->current_balance > 0)
                            <button class="btn btn-sm btn-outline-success py-0 px-2" data-bs-toggle="modal" data-bs-target="#collectModal{{ $customer->id }}">
                                <i class="bi bi-cash"></i> Collect
                            </button>
                            @endif
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-light border py-0 px-2" title="View Customer Profile">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>

                    <!-- Collect Due Modal -->
                    @if($customer->current_balance > 0)
                    <div class="modal fade" id="collectModal{{ $customer->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.customers.payment', $customer) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Collect Payment from {{ $customer->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-warning py-2 mb-3 small">
                                            Current Outstanding Due: <strong>₹{{ number_format($customer->current_balance, 2) }}</strong>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Amount Received (₹) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" name="amount" class="form-control fw-bold" max="{{ $customer->current_balance }}" value="{{ $customer->current_balance }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                                            <select name="payment_method" class="form-select" required>
                                                <option value="cash" selected>Cash</option>
                                                <option value="upi">UPI (GPay / PhonePe / Paytm)</option>
                                                <option value="card">Card</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Reference / Transaction Ref</label>
                                            <input type="text" name="transaction_ref" class="form-control" placeholder="Transaction ref">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label fw-semibold small">Receipt Notes</label>
                                            <input type="text" name="notes" class="form-control" placeholder="Payment receipt notes...">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-success fw-bold">Collect Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Create Customer Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Address</label>
                        <textarea name="address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Opening Due Balance (₹)</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Notes</label>
                        <input type="text" name="notes" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
