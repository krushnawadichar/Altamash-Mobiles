@extends('layouts.admin')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold mb-0">Repair: <span class="text-primary">{{ $repair->repair_code }}</span></h2>
            <span class="badge {{ $repair->status == 'Delivered' ? 'bg-success' : ($repair->status == 'Ready' ? 'bg-info text-dark' : ($repair->status == 'In Progress' ? 'bg-primary' : ($repair->status == 'Waiting for Parts' ? 'bg-warning text-dark' : 'bg-secondary'))) }} fs-6 px-3 py-1">
                {{ $repair->status }}
            </span>
        </div>
        <p class="text-muted mb-0">Received on {{ $repair->received_date->format('d M, Y') }} | Handled by: {{ $repair->handler->name ?? 'Technician' }}</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        @if($repair->due_amount > 0)
            <button type="button" class="btn btn-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#payRepairDueModal">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pay Customer Due
            </button>
        @endif
        <a href="{{ route('admin.repairs.print', $repair) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1"></i> Print Repair Receipt
        </a>
        <a href="{{ route('admin.repairs.edit', $repair) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-outline-dark">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Pending Due Alert Banner -->
@if($repair->due_amount > 0)
<div class="alert alert-warning border-warning border-2 shadow-sm d-flex justify-content-between align-items-center mb-4 p-3 rounded-3">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-warning bg-opacity-25 p-3 text-warning">
            <i class="fa-solid fa-circle-exclamation fs-3"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1 text-dark">Customer Pending Payment Due: ₹{{ number_format($repair->due_amount, 2) }}</h5>
            <p class="mb-0 text-muted small">Customer has paid ₹{{ number_format($repair->paid_amount, 2) }} of the total ₹{{ number_format($repair->effective_total, 2) }} repair bill.</p>
        </div>
    </div>
    <button type="button" class="btn btn-warning text-dark fw-bold px-4" data-bs-toggle="modal" data-bs-target="#payRepairDueModal">
        <i class="fa-solid fa-hand-holding-dollar me-1"></i> Record Payment Now
    </button>
</div>
@endif

<!-- Executive KPI Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Total Customer Bill</span>
                <h4 class="fw-bold text-primary mb-0">₹{{ number_format($repair->effective_total, 2) }}</h4>
                <div class="text-muted small mt-1">Labor + Materials</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Total Material Cost (खर्च)</span>
                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($repair->material_cost, 2) }}</h4>
                <div class="text-muted small mt-1">{{ $repair->items->count() }} spare parts used</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Net Profit (मुनाफा)</span>
                <h4 class="fw-bold text-success mb-0">₹{{ number_format($repair->profit, 2) }}</h4>
                @php
                    $margin = $repair->effective_total > 0 ? round(($repair->profit / $repair->effective_total) * 100, 1) : 0;
                @endphp
                <div class="text-success small mt-1 fw-semibold">{{ $margin }}% Profit Margin</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 border-start {{ $repair->due_amount > 0 ? 'border-warning' : 'border-success' }} border-4 h-100">
            <div class="card-body">
                <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Payment Status</span>
                <h4 class="fw-bold {{ $repair->due_amount > 0 ? 'text-warning' : 'text-success' }} mb-0">
                    {{ $repair->due_amount <= 0 ? 'Fully Paid' : '₹' . number_format($repair->due_amount, 2) . ' Due' }}
                </h4>
                <div class="text-muted small mt-1">Paid: ₹{{ number_format($repair->paid_amount, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Customer Details & Materials Used -->
    <div class="col-lg-7">
        <!-- Customer & Device Details -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-mobile-screen-button text-primary me-2"></i> Device & Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Customer Name</small>
                        <span class="fw-bold fs-6 text-dark">{{ $repair->customer_name }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Customer Phone</small>
                        <span class="fw-semibold text-dark"><i class="fa-solid fa-phone me-1 text-primary"></i>{{ $repair->customer_phone }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Device Model</small>
                        <span class="fw-bold text-dark">{{ $repair->device_name }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted text-uppercase fw-semibold d-block">Expected Delivery</small>
                        <span class="text-dark">{{ $repair->expected_delivery ? $repair->expected_delivery->format('d M, Y') : 'Not Specified' }}</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted text-uppercase fw-semibold d-block">Problem / Fault Reported</small>
                        <div class="p-2 bg-light rounded text-secondary">{{ $repair->problem_description }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materials & Spare Parts Table -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-wrench text-warning me-2"></i> Materials & Spare Parts Replaced</h5>
                <span class="badge bg-light text-dark border">{{ $repair->items->count() }} items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Spare Part / Component</th>
                                <th class="text-center" style="width: 15%;">Qty</th>
                                <th class="text-end" style="width: 25%;">Unit Cost (₹)</th>
                                <th class="text-end" style="width: 25%;">Total Cost (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repair->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                    @if($item->product)
                                        <small class="text-muted">Linked to Inventory SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                                <td class="text-end">₹{{ number_format($item->unit_cost, 2) }}</td>
                                <td class="text-end fw-bold text-danger">₹{{ number_format($item->total_cost, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-box-open fs-3 d-block mb-2"></i>
                                    No materials or spare parts added for this repair.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Material Cost (खर्च):</td>
                                <td class="text-end fw-bold text-danger fs-6">₹{{ number_format($repair->material_cost, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Technician Notes & Payment Log -->
        @if(!empty($repair->technician_notes))
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clock-rotate-left text-info me-2"></i> Payment History & Technician Log</h5>
            </div>
            <div class="card-body">
                <pre class="small text-secondary mb-0 p-3 bg-light rounded border" style="white-space: pre-wrap; font-family: inherit;">{{ $repair->technician_notes }}</pre>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Financial Summary & Actions -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-receipt text-primary me-2"></i> Cost & Profit Breakdown</h5>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tr>
                        <td class="text-muted py-3 ps-3">Total Material / Parts Cost:</td>
                        <td class="text-end fw-bold text-danger py-3 pe-3">₹{{ number_format($repair->material_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-3 ps-3">Service / Labor Charge:</td>
                        <td class="text-end fw-semibold py-3 pe-3">₹{{ number_format($repair->service_charge, 2) }}</td>
                    </tr>
                    <tr class="table-primary fw-bold">
                        <td class="py-3 ps-3 fs-6">Total Customer Bill:</td>
                        <td class="text-end py-3 pe-3 fs-5 text-primary">₹{{ number_format($repair->effective_total, 2) }}</td>
                    </tr>
                    <tr class="table-success fw-bold">
                        <td class="py-3 ps-3 text-success">
                            Net Profit Made (मुनाफा):
                            <small class="d-block text-muted fw-normal">Total Bill - Material Cost</small>
                        </td>
                        <td class="text-end py-3 pe-3 fs-5 text-success">
                            ₹{{ number_format($repair->profit, 2) }}
                            <div class="small fw-normal">{{ $margin }}% margin</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-success fw-semibold py-3 ps-3">Amount Paid by Customer:</td>
                        <td class="text-end fw-bold text-success py-3 pe-3">₹{{ number_format($repair->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="{{ $repair->due_amount > 0 ? 'table-warning' : 'table-light' }}">
                        <td class="fw-bold py-3 ps-3 text-danger">Pending Customer Due:</td>
                        <td class="text-end fw-bold fs-5 py-3 pe-3 {{ $repair->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                            ₹{{ number_format($repair->due_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer bg-light p-3">
                @if($repair->due_amount > 0)
                    <button type="button" class="btn btn-warning text-dark w-100 py-2 fw-bold mb-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#payRepairDueModal">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i> Receive Due Payment
                    </button>
                @endif
                <a href="{{ route('admin.repairs.print', $repair) }}" target="_blank" class="btn btn-outline-secondary w-100 py-2">
                    <i class="fa-solid fa-print me-2"></i> Print Customer Bill & Repair Receipt
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Pay Pending Repair Due -->
<div class="modal fade" id="payRepairDueModal" tabindex="-1" aria-labelledby="payRepairDueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('admin.repairs.pay-due', $repair) }}">
                @csrf
                <div class="modal-header bg-warning bg-opacity-25 border-bottom border-warning border-opacity-50">
                    <h5 class="modal-title fw-bold text-dark" id="payRepairDueModalLabel">
                        <i class="fa-solid fa-hand-holding-dollar text-warning me-2"></i> Pay Customer Pending Due
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 py-2 px-3">
                        <div>
                            <small class="text-muted d-block">Customer & Device</small>
                            <span class="fw-bold text-dark">{{ $repair->customer_name }}</span>
                            <div class="small text-muted">{{ $repair->device_name }} ({{ $repair->repair_code }})</div>
                        </div>
                        <div class="text-end">
                            <small class="text-danger fw-semibold d-block">Outstanding Balance</small>
                            <span class="fs-5 fw-bold text-danger">₹{{ number_format($repair->due_amount, 2) }}</span>
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
                                   max="{{ $repair->due_amount }}" 
                                   value="{{ $repair->due_amount }}" 
                                   required>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnFillFullDue">Full Due (₹{{ number_format($repair->due_amount, 2) }})</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnFillHalfDue">50% Due</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="Cash" selected>Cash</option>
                                <option value="UPI">UPI (GPay / PhonePe / Paytm)</option>
                                <option value="Card">Debit / Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer (NEFT/IMPS)</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted text-uppercase">UTR / Txn / Ref #</label>
                            <input type="text" name="payment_reference" class="form-control" placeholder="e.g. UTR198274921">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Payment Note / Remark</label>
                        <input type="text" name="payment_notes" class="form-control" placeholder="e.g. Cleared at delivery handoff">
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
    const totalDue = {{ $repair->due_amount }};
    const modalPayAmount = document.getElementById('modalPayAmount');

    document.getElementById('btnFillFullDue')?.addEventListener('click', function() {
        modalPayAmount.value = totalDue.toFixed(2);
    });

    document.getElementById('btnFillHalfDue')?.addEventListener('click', function() {
        modalPayAmount.value = (totalDue * 0.5).toFixed(2);
    });
});
</script>
@endpush
