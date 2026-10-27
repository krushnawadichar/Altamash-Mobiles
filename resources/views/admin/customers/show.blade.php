@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-decoration-none">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user text-primary me-2"></i>{{ $customer->name }}</h2>
            <p class="text-muted small mb-0">Customer Profile, Invoices, and Outstanding Due Balances</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Customers
            </a>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary">
                <i class="fa-solid fa-edit me-1"></i> Edit Customer
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Customer Overview Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="fa-solid fa-phone fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Contact Phone</span>
                        <span class="fw-bold fs-6 text-dark">{{ $customer->phone ?? 'Not Provided' }}</span>
                        @if($customer->email)
                            <small class="text-muted d-block">{{ $customer->email }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="fa-solid fa-bag-shopping fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Purchases</span>
                        <span class="fw-bold fs-5 text-success">₹{{ number_format($customer->total_purchases, 2) }}</span>
                        <small class="text-muted d-block">{{ $customer->sales->count() }} Invoice(s)</small>
                    </div>
                </div>
            </div>
        </div>

        @php $totalDue = $customer->total_due; @endphp
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 {{ $totalDue > 0 ? 'border-danger border-2' : '' }}">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle {{ $totalDue > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} p-3 me-3">
                        <i class="fa-solid fa-hand-holding-dollar fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Outstanding Due (Udhaar)</span>
                        <span class="fw-bold fs-4 {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">
                            ₹{{ number_format($totalDue, 2) }}
                        </span>
                        @if($totalDue > 0)
                            <small class="text-danger fw-semibold d-block">Customer has pending balance</small>
                        @else
                            <small class="text-success d-block">All invoices cleared</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Invoices & Order History -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Invoices &amp; Payment History
            </h5>
            <span class="badge bg-light text-dark border">{{ $customer->sales->count() }} Invoices</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Grand Total</th>
                            <th>Paid Amount</th>
                            <th>Pending Due</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('admin.sales.show', $sale) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                            <td class="fw-bold text-dark">₹{{ number_format($sale->grand_total, 2) }}</td>
                            <td class="text-success fw-semibold">₹{{ number_format($sale->paid_amount, 2) }}</td>
                            <td>
                                @if($sale->due_amount > 0)
                                    <span class="fw-bold text-danger">₹{{ number_format($sale->due_amount, 2) }}</span>
                                @else
                                    <span class="text-muted">₹0.00</span>
                                @endif
                            </td>
                            <td>
                                @if($sale->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($sale->payment_status == 'partial')
                                    <span class="badge bg-warning text-dark">Partial Paid</span>
                                @else
                                    <span class="badge bg-danger">Unpaid</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($sale->due_amount > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-success fw-semibold" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#payModal{{ $sale->id }}">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pay Due
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-outline-info" title="View Invoice">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-success text-white" 
                                            title="Send Invoice on WhatsApp"
                                            onclick="openWhatsAppModal('{{ $sale->invoice_number }}', '{{ addslashes($customer->name) }}', '{{ $customer->phone ?? '' }}', '{{ number_format($sale->grand_total, 2) }}', '{{ number_format($sale->paid_amount, 2) }}', '{{ number_format($sale->due_amount, 2) }}', '{{ route('invoice.public', $sale->invoice_number) }}')">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </button>
                                    <a href="{{ route('admin.sales.print', $sale) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print Invoice">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-file-invoice fs-1 d-block mb-2 text-secondary"></i>
                                <span>No invoices recorded for this customer yet.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modals for each unpaid sale -->
@foreach($customer->sales->where('due_amount', '>', 0) as $sale)
<div class="modal fade" id="payModal{{ $sale->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.sales.pay-due', $sale) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i>Receive Due Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded border mb-3 text-center">
                        <span class="text-muted small d-block">Pending Due on Invoice</span>
                        <span class="fs-3 fw-bold text-danger">₹{{ number_format($sale->due_amount, 2) }}</span>
                        <small class="text-muted d-block mt-1">Invoice: <strong>{{ $sale->invoice_number }}</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Amount Received (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="amount" 
                                   class="form-control form-control-lg fw-bold number-only" 
                                   value="{{ $sale->due_amount }}" 
                                   max="{{ $sale->due_amount }}"
                                   required>
                        </div>
                        <small class="text-muted">Enter full or partial payment amount</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select form-select-lg" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI / GPay / PhonePe</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Payment Note (Optional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Paid in cash by {{ $customer->name }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-check-circle me-1"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
