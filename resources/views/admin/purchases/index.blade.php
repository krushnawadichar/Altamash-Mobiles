@extends('layouts.admin')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold mb-1">Purchases Management</h2>
        <p class="text-muted mb-0">Track supplier bills, purchase invoices, inventory restocking, and pending payments</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-users me-1"></i> Suppliers
        </a>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> New Purchase Bill
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Purchases</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                        <i class="fa-solid fa-cart-shopping fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0">₹{{ number_format($totalPurchases ?? 0, 2) }}</h4>
                <div class="text-muted small mt-1">{{ $totalCount ?? 0 }} total invoices</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Paid</span>
                    <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                        <i class="fa-solid fa-check-circle fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-success mb-0">₹{{ number_format($totalPaid ?? 0, 2) }}</h4>
                <div class="text-muted small mt-1">Settled to suppliers</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Supplier Due Balance</span>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger">
                        <i class="fa-solid fa-hourglass-half fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($totalDue ?? 0, 2) }}</h4>
                <div class="text-danger small mt-1 fw-medium">Pending to be paid</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Payment Health</span>
                    <div class="rounded-circle bg-info bg-opacity-10 p-2 text-info">
                        <i class="fa-solid fa-chart-pie fs-5"></i>
                    </div>
                </div>
                @php
                    $clearanceRate = ($totalPurchases ?? 0) > 0 ? round((($totalPaid ?? 0) / $totalPurchases) * 100, 1) : 100;
                @endphp
                <h4 class="fw-bold text-info mb-0">{{ $clearanceRate }}%</h4>
                <div class="text-muted small mt-1">Paid clearance ratio</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-invoice text-primary me-2"></i> Purchase Invoices</h5>
        <span class="badge bg-light text-dark border">{{ count($purchases) }} entries</span>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Supplier</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y') : 'N/A' }}</div>
                            <small class="text-muted">{{ $purchase->purchase_date ? $purchase->purchase_date->format('h:i A') : '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary fw-mono px-2 py-1">
                                {{ $purchase->invoice_number ?? 'PO-#'.$purchase->id }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $purchase->supplier->name ?? 'Unknown Supplier' }}</div>
                            @if(!empty($purchase->supplier->phone))
                                <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $purchase->supplier->phone }}</small>
                            @endif
                        </td>
                        <td class="text-end fw-bold">₹{{ number_format($purchase->grand_total, 2) }}</td>
                        <td class="text-end text-success fw-semibold">₹{{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="text-end">
                            @if($purchase->due_amount > 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 px-2 py-1">
                                    ₹{{ number_format($purchase->due_amount, 2) }}
                                </span>
                            @else
                                <span class="text-muted small">₹0.00</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($purchase->payment_status == 'paid')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fa-solid fa-check me-1"></i> Fully Paid
                                </span>
                            @elseif($purchase->payment_status == 'partial')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="fa-solid fa-clock me-1"></i> Partial
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> Unpaid
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                @if($purchase->due_amount > 0)
                                    <button type="button" 
                                            class="btn btn-warning text-dark btn-quick-pay"
                                            title="Pay Pending Supplier Due"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#quickPayDueModal"
                                            data-action="{{ route('admin.purchases.pay-due', $purchase) }}"
                                            data-invoice="{{ $purchase->invoice_number ?? 'PO-#'.$purchase->id }}"
                                            data-supplier="{{ $purchase->supplier->name ?? 'Supplier' }}"
                                            data-due="{{ $purchase->due_amount }}"
                                            data-total="{{ $purchase->grand_total }}">
                                        <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pay Due
                                    </button>
                                @endif
                                <a href="{{ route('admin.purchases.show', $purchase) }}" class="btn btn-outline-info" title="View Bill & History">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.purchases.print', $purchase) }}" target="_blank" class="btn btn-outline-secondary" title="Print Purchase Voucher">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Quick Pay Due from Purchases Index -->
<div class="modal fade" id="quickPayDueModal" tabindex="-1" aria-labelledby="quickPayDueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="quickPayDueForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-warning bg-opacity-25 border-bottom border-warning border-opacity-50">
                    <h5 class="modal-title fw-bold text-dark" id="quickPayDueModalLabel">
                        <i class="fa-solid fa-hand-holding-dollar text-warning me-2"></i> Pay Supplier Due
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 py-2 px-3">
                        <div>
                            <small class="text-muted d-block">Supplier & Bill</small>
                            <span class="fw-bold text-dark" id="modalSupplierName">-</span>
                            <div class="small text-muted" id="modalInvoiceNo">-</div>
                        </div>
                        <div class="text-end">
                            <small class="text-danger fw-semibold d-block">Outstanding Due</small>
                            <span class="fs-5 fw-bold text-danger" id="modalDueDisplay">₹0.00</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Payment Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold">₹</span>
                            <input type="number" 
                                   name="amount" 
                                   id="modalPayAmount" 
                                   class="form-control form-control-lg fw-bold" 
                                   step="0.01" 
                                   min="0.01" 
                                   required>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnFillFullDue">Full Due</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnFillHalfDue">50% Due</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnFillQuarterDue">25% Due</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Bank Transfer">Bank Transfer (NEFT/IMPS)</option>
                                <option value="UPI" selected>UPI (GPay / PhonePe / Paytm)</option>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase">UTR / Cheque / Ref #</label>
                            <input type="text" name="payment_reference" class="form-control" placeholder="e.g. UTR198274921">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Payment Note / Remark</label>
                        <input type="text" name="payment_notes" class="form-control" placeholder="e.g. Settled balance via business current account">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-check me-1"></i> Confirm & Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDue = 0;
    const modalForm = document.getElementById('quickPayDueForm');
    const modalSupplierName = document.getElementById('modalSupplierName');
    const modalInvoiceNo = document.getElementById('modalInvoiceNo');
    const modalDueDisplay = document.getElementById('modalDueDisplay');
    const modalPayAmount = document.getElementById('modalPayAmount');

    document.querySelectorAll('.btn-quick-pay').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const supplier = this.getAttribute('data-supplier');
            const invoice = this.getAttribute('data-invoice');
            currentDue = parseFloat(this.getAttribute('data-due')) || 0;

            modalForm.action = action;
            modalSupplierName.textContent = supplier;
            modalInvoiceNo.textContent = invoice;
            modalDueDisplay.textContent = '₹' + currentDue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            modalPayAmount.value = currentDue.toFixed(2);
            modalPayAmount.max = currentDue.toFixed(2);
        });
    });

    document.getElementById('btnFillFullDue').addEventListener('click', function() {
        modalPayAmount.value = currentDue.toFixed(2);
    });

    document.getElementById('btnFillHalfDue').addEventListener('click', function() {
        modalPayAmount.value = (currentDue * 0.5).toFixed(2);
    });

    document.getElementById('btnFillQuarterDue').addEventListener('click', function() {
        modalPayAmount.value = (currentDue * 0.25).toFixed(2);
    });
});
</script>
@endpush



