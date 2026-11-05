@extends('layouts.admin')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-7">
        <h2 class="fw-bold mb-1">Repairs & Services Management</h2>
        <p class="text-muted mb-0">Track device repairs, materials used, service fees, customer payments, and net profit</p>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
        <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> New Repair
        </a>
    </div>
</div>

<!-- Executive Financial KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Repairs</span>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary">
                        <i class="fa-solid fa-screwdriver-wrench fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-0">{{ $totalRepairs ?? 0 }}</h4>
                <div class="text-muted small mt-1">Devices serviced</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Total Billed</span>
                    <div class="rounded-circle bg-info bg-opacity-10 p-2 text-info">
                        <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-info mb-0">₹{{ number_format($totalBilled ?? 0, 2) }}</h4>
                <div class="text-muted small mt-1">Customer invoices</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Material Cost</span>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-2 text-danger">
                        <i class="fa-solid fa-boxes-packing fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-danger mb-0">₹{{ number_format($totalMaterialCost ?? 0, 2) }}</h4>
                <div class="text-muted small mt-1">Spare parts expense</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Net Profit (मुनाफा)</span>
                    <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success">
                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-success mb-0">₹{{ number_format($totalProfit ?? 0, 2) }}</h4>
                @php
                    $overallMargin = ($totalBilled ?? 0) > 0 ? round((($totalProfit ?? 0) / $totalBilled) * 100, 1) : 0;
                @endphp
                <div class="text-success small mt-1 fw-semibold">{{ $overallMargin }}% profit margin</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small text-uppercase fw-semibold">Customer Due</span>
                    <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning">
                        <i class="fa-solid fa-clock fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-warning mb-0">₹{{ number_format($totalDue ?? 0, 2) }}</h4>
                <div class="text-muted small mt-1">Pending collection</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i> All Repairs</h5>
        <span class="badge bg-light text-dark border">{{ count($repairs) }} entries</span>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Repair Code & Date</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th class="text-end">Material Cost</th>
                        <th class="text-end">Total Bill</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due (बाकी)</th>
                        <th class="text-end">Profit (मुनाफा)</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repairs as $repair)
                    @php
                        $billAmount = $repair->effective_total;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('admin.repairs.show', $repair) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $repair->repair_code }}
                            </a>
                            <small class="text-muted d-block">{{ $repair->received_date->format('d M, Y') }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $repair->customer_name }}</div>
                            <small class="text-muted"><i class="fa-solid fa-phone me-1"></i>{{ $repair->customer_phone }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ Str::limit($repair->device_name, 22) }}</div>
                            <small class="text-muted">{{ Str::limit($repair->problem_description, 25) }}</small>
                        </td>
                        <td class="text-end text-danger fw-semibold">
                            ₹{{ number_format($repair->material_cost, 2) }}
                            @if($repair->items->count() > 0)
                                <small class="text-muted d-block">{{ $repair->items->count() }} parts</small>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-dark">
                            ₹{{ number_format($billAmount, 2) }}
                        </td>
                        <td class="text-end text-success fw-semibold">
                            ₹{{ number_format($repair->paid_amount, 2) }}
                        </td>
                        <td class="text-end">
                            @if($repair->due_amount > 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-6 px-2 py-1">
                                    ₹{{ number_format($repair->due_amount, 2) }}
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="fa-solid fa-check me-1"></i> Paid
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-6 px-2 py-1">
                                +₹{{ number_format($repair->profit, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($repair->status == 'Received')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Received</span>
                            @elseif($repair->status == 'In Progress')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">In Progress</span>
                            @elseif($repair->status == 'Waiting for Parts')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Waiting for Parts</span>
                            @elseif($repair->status == 'Ready')
                                <span class="badge bg-info-subtle text-info border border-info-subtle">Ready</span>
                            @elseif($repair->status == 'Delivered')
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Delivered</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ $repair->status }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                @if($repair->due_amount > 0)
                                    <button type="button" 
                                            class="btn btn-warning text-dark btn-quick-pay-repair"
                                            title="Receive Pending Due Payment"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#quickPayRepairModal"
                                            data-action="{{ route('admin.repairs.pay-due', $repair) }}"
                                            data-code="{{ $repair->repair_code }}"
                                            data-customer="{{ $repair->customer_name }}"
                                            data-device="{{ $repair->device_name }}"
                                            data-due="{{ $repair->due_amount }}">
                                        <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pay Due
                                    </button>
                                @endif
                                <a href="{{ route('admin.repairs.show', $repair) }}" class="btn btn-outline-info" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.repairs.print', $repair) }}" target="_blank" class="btn btn-outline-secondary" title="Print Repair Receipt">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <a href="{{ route('admin.repairs.edit', $repair) }}" class="btn btn-outline-primary" title="Edit Repair">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.repairs.destroy', $repair) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this repair?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
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

<!-- Modal: Quick Pay Repair Due from Index -->
<div class="modal fade" id="quickPayRepairModal" tabindex="-1" aria-labelledby="quickPayRepairModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="quickPayRepairForm" method="POST" action="">
                @csrf
                <div class="modal-header bg-warning bg-opacity-25 border-bottom border-warning border-opacity-50">
                    <h5 class="modal-title fw-bold text-dark" id="quickPayRepairModalLabel">
                        <i class="fa-solid fa-hand-holding-dollar text-warning me-2"></i> Receive Customer Repair Due
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 py-2 px-3">
                        <div>
                            <small class="text-muted d-block">Customer & Device</small>
                            <span class="fw-bold text-dark" id="modalCustomerName">-</span>
                            <div class="small text-muted" id="modalDeviceCode">-</div>
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
                        <input type="text" name="payment_notes" class="form-control" placeholder="e.g. Received at delivery handover">
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
    const modalForm = document.getElementById('quickPayRepairForm');
    const modalCustomerName = document.getElementById('modalCustomerName');
    const modalDeviceCode = document.getElementById('modalDeviceCode');
    const modalDueDisplay = document.getElementById('modalDueDisplay');
    const modalPayAmount = document.getElementById('modalPayAmount');

    document.querySelectorAll('.btn-quick-pay-repair').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const code = this.getAttribute('data-code');
            const customer = this.getAttribute('data-customer');
            const device = this.getAttribute('data-device');
            currentDue = parseFloat(this.getAttribute('data-due')) || 0;

            modalForm.action = action;
            modalCustomerName.textContent = customer;
            modalDeviceCode.textContent = device + ' (' + code + ')';
            modalDueDisplay.textContent = '₹' + currentDue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            modalPayAmount.value = currentDue.toFixed(2);
            modalPayAmount.max = currentDue.toFixed(2);
        });
    });

    document.getElementById('btnFillFullDue')?.addEventListener('click', function() {
        modalPayAmount.value = currentDue.toFixed(2);
    });

    document.getElementById('btnFillHalfDue')?.addEventListener('click', function() {
        modalPayAmount.value = (currentDue * 0.5).toFixed(2);
    });
});
</script>
@endpush
