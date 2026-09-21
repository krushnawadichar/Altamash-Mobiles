@extends('layouts.admin')

@section('title', 'Job Card ' . $repair->repair_no)
@section('page_title', 'Repair Job Card Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Job Card: {{ $repair->repair_no }}</h4>
            <span class="badge {{ $repair->status_badge_class }} fs-6">{{ $repair->status }}</span>
            <span class="text-muted ms-2 small">Intake: {{ $repair->received_date->format('d M Y, h:i A') }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                <i class="bi bi-arrow-repeat me-1"></i> Update Status
            </button>
            <button class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addPartModal">
                <i class="bi bi-plus-circle me-1"></i> Add Spare Part
            </button>
            @if($repair->due_amount > 0)
                <button class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                    <i class="bi bi-cash me-1"></i> Collect Payment
                </button>
            @endif
            <a href="{{ route('admin.repairs.jobcard', $repair) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-printer me-1"></i> Print Job Card
            </a>
            <a href="{{ route('admin.repairs.invoice', $repair) }}" target="_blank" class="btn btn-dark btn-sm">
                <i class="bi bi-receipt me-1"></i> Print Delivery Bill
            </a>
            <a href="{{ route('admin.repairs.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Status Progress Bar (12 statuses) -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold small text-muted text-uppercase">Repair Workflow Progress:</span>
                <span class="badge {{ $repair->status_badge_class }}">{{ $repair->status }}</span>
            </div>
            @php
                $statusSteps = [
                    'Received' => 10,
                    'Diagnosis Pending' => 20,
                    'Under Diagnosis' => 30,
                    'Estimate Given' => 40,
                    'Customer Approval Pending' => 50,
                    'Approved' => 60,
                    'Repairing' => 70,
                    'Waiting for Parts' => 75,
                    'Repair Completed' => 85,
                    'Ready for Delivery' => 95,
                    'Delivered' => 100,
                    'Cancelled' => 100,
                ];
                $percent = $statusSteps[$repair->status] ?? 15;
                $barColor = $repair->status === 'Cancelled' ? 'bg-secondary' : ($repair->status === 'Delivered' ? 'bg-success' : 'bg-primary');
            @endphp
            <div class="progress" style="height: 10px;">
                <div class="progress-bar {{ $barColor }} progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $percent }}%;"></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Left Col: Problem, Parts Used, Status Logs -->
        <div class="col-lg-8">
            <!-- Device & Complaint Details -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-phone me-1 text-primary"></i>Device & Complaint Description</span>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Device Model</small>
                            <span class="fw-bold fs-6">{{ $repair->model_name }}</span>
                            <div class="small text-muted">{{ $repair->brand?->name ?? '' }} {{ $repair->color ? '(' . $repair->color . ')' : '' }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">IMEI Number</small>
                            <code class="text-dark bg-light px-2 py-1 rounded border">{{ $repair->imei ?: 'Not provided' }}</code>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Serial Number</small>
                            <span class="font-monospace">{{ $repair->serial_no ?: '-' }}</span>
                        </div>
                        <div class="col-12 border-top pt-2">
                            <small class="text-muted d-block fw-semibold text-danger">Reported Problem / Complaint:</small>
                            <div class="p-2 bg-danger-subtle rounded text-danger-emphasis mt-1 fw-medium">
                                {{ $repair->problem_complaint }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Physical Condition on Intake:</small>
                            <span>{{ $repair->physical_condition ?: 'Standard condition' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Accessories Received:</small>
                            <span>{{ $repair->accessories_received ?: 'Device only' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spare Parts Used in Repair (Connected with Inventory!) -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-tools me-1 text-primary"></i>Spare Parts Consumed from Inventory ({{ $repair->partsUsed->count() }})</span>
                    <button class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#addPartModal">
                        <i class="bi bi-plus"></i> Add Part
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Spare Part</th>
                                <th>Quantity Used</th>
                                <th>Billed Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($repair->partsUsed as $part)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $part->product->name }}</div>
                                    <small class="text-muted">SKU: {{ $part->product->sku }}</small>
                                </td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $part->quantity }}</span></td>
                                <td>₹{{ number_format($part->unit_price, 2) }}</td>
                                <td class="text-end fw-bold">₹{{ number_format($part->subtotal, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No spare parts deducted yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Status Activity Timeline -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-clock-history me-1 text-secondary"></i>Workflow Status Logs</span>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0">
                        @foreach($repair->statusLogs as $log)
                        <li class="d-flex gap-3 mb-3">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                <i class="bi bi-check2"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">
                                    {{ $log->from_status }} &rarr; <span class="text-primary">{{ $log->to_status }}</span>
                                </div>
                                @if($log->notes)
                                    <div class="text-secondary small">{{ $log->notes }}</div>
                                @endif
                                <small class="text-muted" style="font-size: 0.72rem;">
                                    {{ $log->created_at->format('d M Y, h:i A') }} &bull; By {{ $log->creator?->name ?? 'Staff' }}
                                </small>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Col: Financial, Customer, Technician -->
        <div class="col-lg-4">
            <!-- Cost & Balance Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-currency-rupee me-1 text-success"></i>Billing & Payments</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Estimated Cost:</span>
                        <span class="fw-semibold">₹{{ number_format($repair->estimated_cost, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Spare Parts Total:</span>
                        <span class="fw-semibold">₹{{ number_format($repair->partsUsed->sum('subtotal'), 2) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-2 fs-5">
                        <span class="fw-bold">Final Billed Cost:</span>
                        <span class="fw-bold text-dark">₹{{ number_format($repair->final_cost, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Total Paid (Advance + Paid):</span>
                        <span class="fw-bold">₹{{ number_format($repair->paid_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 {{ $repair->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                        <span class="fw-bold">Balance Remaining:</span>
                        <span class="fw-bold fs-5">₹{{ number_format($repair->due_amount, 2) }}</span>
                    </div>

                    @if($repair->due_amount > 0)
                    <button class="btn btn-success btn-sm w-100 fw-bold" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="bi bi-cash-coin me-1"></i> Collect Payment
                    </button>
                    @endif
                </div>
            </div>

            <!-- Customer & Technician Details -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-person me-1 text-secondary"></i>Customer Information</span>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1">{{ $repair->customer_name }}</h6>
                    <div class="small mb-1"><i class="bi bi-telephone me-1 text-muted"></i>+91 {{ $repair->customer_mobile }}</div>
                    @if($repair->customer?->address)
                        <div class="small text-muted">{{ $repair->customer->address }}</div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-person-badge me-1 text-info"></i>Assigned Technician</span>
                </div>
                <div class="card-body p-3">
                    @if($repair->technician)
                        <h6 class="fw-bold mb-1">{{ $repair->technician->name }}</h6>
                        <div class="small text-muted">{{ $repair->technician->specialization ?: 'Mobile Service Specialist' }}</div>
                        <div class="small mt-1"><i class="bi bi-telephone me-1 text-muted"></i>{{ $repair->technician->mobile }}</div>
                    @else
                        <div class="text-muted small">No technician assigned yet.</div>
                    @endif
                    <div class="mt-2 pt-2 border-top small">
                        <span class="text-muted">Expected Delivery:</span>
                        <strong>{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d M Y') : 'Not set' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.repairs.update-status', $repair) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Update Repair Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">New Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ $repair->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Re-assign Technician</label>
                        <select name="technician_id" class="form-select select2">
                            <option value="">Keep Current Technician</option>
                            @foreach($technicians as $t)
                                <option value="{{ $t->id }}" {{ $repair->technician_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Technician Remarks / Status Note</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="What diagnostic/repair work was done?"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Add Spare Part from Inventory -->
<div class="modal fade" id="addPartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.repairs.add-part', $repair) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Spare Part from Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Adding a spare part will <strong>automatically deduct it from your inventory</strong> and record a <code>REPAIR_PART_USED</code> ledger transaction.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Select Component / Part <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select select2" required>
                            <option value="">Choose Component...</option>
                            @foreach($spareParts as $part)
                                <option value="{{ $part->id }}">{{ $part->name }} (Available Stock: {{ $part->current_stock }}) - ₹{{ number_format($part->selling_price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Custom Billed Price (₹, optional override)</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="Leave empty for standard selling price">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Deduct & Add to Repair</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Collect Payment -->
@if($repair->due_amount > 0)
<div class="modal fade" id="collectPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.repairs.payment', $repair) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Collect Repair Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 mb-3 small">
                        Remaining Repair Due: <strong>₹{{ number_format($repair->due_amount, 2) }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold" max="{{ $repair->due_amount }}" value="{{ $repair->due_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Type</label>
                        <select name="payment_type" class="form-select">
                            <option value="final" selected>Final Settlement (Delivery)</option>
                            <option value="partial">Partial Payment</option>
                            <option value="advance">Additional Advance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" selected>Cash</option>
                            <option value="upi">UPI / QR Scan</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Transaction Reference</label>
                        <input type="text" name="transaction_ref" class="form-control" placeholder="Transaction ref / UPI ID">
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
@endif
@endsection
