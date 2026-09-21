@extends('layouts.admin')

@section('title', 'Customer - ' . $customer->name)
@section('page_title', 'Customer Profile & Ledger')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">{{ $customer->name }}</h4>
            <small class="text-muted"><i class="bi bi-telephone me-1"></i>+91 {{ $customer->mobile }} &bull; {{ $customer->email ?: 'No email' }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.pos.index') }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-cart-plus me-1"></i> New POS Sale
            </a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Purchases</div>
                <h4 class="fw-bold mb-0 mt-1">₹{{ number_format($customer->total_purchases, 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Paid Amount</div>
                <h4 class="fw-bold mb-0 mt-1 text-success">₹{{ number_format($customer->total_paid, 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Outstanding Balance (Due)</div>
                <h4 class="fw-bold mb-0 mt-1 {{ $customer->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                    ₹{{ number_format($customer->current_balance, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Sales & Repairs History -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-receipt me-1 text-primary"></i>Purchase Invoices ({{ $customer->sales->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Status</th>
                                <th class="text-end">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->sales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="fw-bold font-monospace text-primary text-decoration-none">
                                        {{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td class="fw-bold">₹{{ number_format($sale->grand_total, 2) }}</td>
                                <td class="text-success">₹{{ number_format($sale->paid_amount, 2) }}</td>
                                <td class="{{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    ₹{{ number_format($sale->due_amount, 2) }}
                                </td>
                                <td><span class="badge {{ $sale->payment_badge_class }}">{{ ucfirst($sale->payment_status) }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-light border py-0 px-2"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">No sales invoices found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Repair Jobs History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-tools me-1 text-primary"></i>Repair Jobs ({{ $customer->repairJobs->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Job No</th>
                                <th>Device</th>
                                <th>Problem</th>
                                <th>Cost</th>
                                <th>Status</th>
                                <th class="text-end">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->repairJobs as $rep)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.repairs.show', $rep) }}" class="fw-bold font-monospace text-decoration-none">
                                        {{ $rep->repair_no }}
                                    </a>
                                </td>
                                <td>{{ $rep->model_name }}</td>
                                <td><small class="text-muted">{{ Str::limit($rep->problem_complaint, 30) }}</small></td>
                                <td class="fw-bold">₹{{ number_format($rep->final_cost, 2) }}</td>
                                <td><span class="badge {{ $rep->status_badge_class }}">{{ $rep->status }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.repairs.show', $rep) }}" class="btn btn-sm btn-light border py-0 px-2"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No repair jobs for this customer.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Col: Profile & Payments -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-person-lines-fill me-1 text-secondary"></i>Customer Profile</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2"><small class="text-muted d-block">Full Name</small> <strong>{{ $customer->name }}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Phone</small> <strong>+91 {{ $customer->mobile }}</strong></div>
                    <div class="mb-2"><small class="text-muted d-block">Email</small> <span>{{ $customer->email ?: '-' }}</span></div>
                    <div class="mb-0"><small class="text-muted d-block">Address</small> <span>{{ $customer->address ?: '-' }}</span></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-wallet2 me-1 text-success"></i>Payment History</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->payments as $pmt)
                            <tr>
                                <td>{{ $pmt->payment_date->format('d M Y') }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($pmt->amount, 2) }}</td>
                                <td><span class="badge bg-light text-dark border text-uppercase">{{ $pmt->payment_method }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-2">No payments logged yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
