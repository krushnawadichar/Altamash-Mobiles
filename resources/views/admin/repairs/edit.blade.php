@extends('layouts.admin')

@push('styles')
<style>
    /* Remove all browser up/down arrows/spinners from number inputs */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
        appearance: textfield !important;
    }
    .problem-chip {
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }
    .problem-chip:hover {
        background-color: #e2e8f0;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold mb-1">Edit Repair: <span class="text-primary">{{ $repair->repair_code }}</span></h2>
        <p class="text-muted mb-0">Update customer & device details, spare parts used, editable charges & real-time profit</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="{{ route('admin.repairs.show', $repair) }}" class="btn btn-outline-info">
            <i class="fa-solid fa-eye me-1"></i> View Repair
        </a>
        <a href="{{ route('admin.repairs.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Repairs
        </a>
    </div>
</div>

<form action="{{ route('admin.repairs.update', $repair) }}" method="POST" id="repairEditForm">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Customer & Device Info -->
        <div class="col-lg-7">
            <!-- 1. Customer & Device Details -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-mobile-screen-button text-primary me-2"></i> 1. Customer & Device Details
                    </h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Step 1</span>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $repair->customer_name) }}" required placeholder="Customer name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" name="customer_phone" class="form-control mobile-number" value="{{ old('customer_phone', $repair->customer_phone) }}" maxlength="10" placeholder="10-digit mobile number" required>
                            </div>
                            <small class="text-muted">Enter 10-digit mobile number</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mobile Model / Device Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-mobile"></i></span>
                            <input type="text" name="device_name" class="form-control" value="{{ old('device_name', $repair->device_name) }}" required placeholder="e.g. iPhone 13 Pro / Samsung S23">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold mb-0">Problem / Issue Reported <span class="text-danger">*</span></label>
                            <small class="text-muted">Click quick pills to append</small>
                        </div>
                        <!-- Quick Problem Suggestion Pills -->
                        <div class="d-flex flex-wrap gap-1 mb-2">
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Screen / Display Broken">📱 Screen / Display</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Battery Drain / Dead">🔋 Battery Problem</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Charging Jack / Port">⚡ Charging Port</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Water Damage">💧 Water Damage</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Speaker / Mic Issue">🔊 Speaker / Mic</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Camera Issue">📷 Camera Issue</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Network / WiFi Issue">📶 Network / WiFi</span>
                            <span class="badge bg-light text-dark border problem-chip py-1 px-2" data-problem="Software / Flashing / FRP">💻 Software / FRP</span>
                        </div>
                        <textarea name="problem_description" id="problemDescriptionInput" class="form-control" rows="2" required>{{ old('problem_description', $repair->problem_description) }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Received Date <span class="text-danger">*</span></label>
                            <input type="date" name="received_date" class="form-control" value="{{ old('received_date', $repair->received_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expected Delivery Date</label>
                            <input type="date" name="expected_delivery" class="form-control" value="{{ old('expected_delivery', $repair->expected_delivery ? $repair->expected_delivery->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Materials & Spare Parts Used (Optional) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fa-solid fa-wrench text-warning me-2"></i> 2. Spare Parts / Materials
                            <span class="badge bg-secondary-subtle text-secondary border ms-2 fw-normal fs-7">Optional</span>
                        </h5>
                        <small class="text-muted">Parts replaced during repair (or enter lump-sum material cost on the right)</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddMaterial">
                        <i class="fa-solid fa-plus me-1"></i> Add Part Row
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="materialsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 44%;">Material / Spare Part</th>
                                    <th style="width: 14%;" class="text-center">Qty</th>
                                    <th style="width: 18%;" class="text-end">Cost (₹)</th>
                                    <th style="width: 18%;" class="text-end">Total (₹)</th>
                                    <th style="width: 6%;" class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="materialsContainer">
                                @forelse($repair->items as $item)
                                <tr class="material-row">
                                    <td>
                                        <div class="input-group input-group-sm mb-1">
                                            <select class="form-select form-select-sm part-preset-select">
                                                <option value="">-- Pick from Inventory (Optional) --</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->name }}" data-id="{{ $prod->id }}" data-cost="{{ $prod->purchase_price ?? 0 }}" {{ $item->product_id == $prod->id ? 'selected' : '' }}>
                                                        {{ $prod->name }} (Cost: ₹{{ number_format($prod->purchase_price ?? 0, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="material_product_ids[]" class="part-product-id" value="{{ $item->product_id }}">
                                        <input type="text" name="material_names[]" class="form-control form-control-sm part-name" value="{{ $item->item_name }}" placeholder="Part name">
                                    </td>
                                    <td>
                                        <input type="number" name="material_quantities[]" class="form-control form-control-sm text-center part-qty" value="{{ $item->quantity }}" min="1">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="material_costs[]" class="form-control form-control-sm text-end part-cost" value="{{ $item->unit_cost > 0 ? $item->unit_cost : '' }}" placeholder="0.00" min="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-end part-subtotal bg-light" value="{{ $item->total_cost > 0 ? '₹' . number_format($item->total_cost, 2) : '' }}" placeholder="0.00" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-row" title="Remove part row">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr class="material-row">
                                    <td>
                                        <div class="input-group input-group-sm mb-1">
                                            <select class="form-select form-select-sm part-preset-select">
                                                <option value="">-- Pick from Inventory (Optional) --</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->name }}" data-id="{{ $prod->id }}" data-cost="{{ $prod->purchase_price ?? 0 }}">
                                                        {{ $prod->name }} (Cost: ₹{{ number_format($prod->purchase_price ?? 0, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="material_product_ids[]" class="part-product-id" value="">
                                        <input type="text" name="material_names[]" class="form-control form-control-sm part-name" placeholder="Type part name (e.g. OLED Display, 5000mAh Battery)">
                                    </td>
                                    <td>
                                        <input type="number" name="material_quantities[]" class="form-control form-control-sm text-center part-qty" value="1" min="1">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="material_costs[]" class="form-control form-control-sm text-end part-cost" placeholder="0.00" value="" min="0">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-end part-subtotal bg-light" placeholder="0.00" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-row" title="Remove part row">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Parts Table Total:</td>
                                    <td class="text-end fw-bold text-danger fs-6" id="displayTableMaterialCost">₹{{ number_format($repair->material_cost, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Technician Notes & Log -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-clipboard-list text-secondary me-2"></i> Technician Notes & Payment History</h5>
                </div>
                <div class="card-body">
                    <textarea name="technician_notes" class="form-control font-monospace small" rows="4">{{ old('technician_notes', $repair->technician_notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Costs, Billing & Profit Calculations (All Editable, Zero defaults removed) -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-calculator text-primary me-2"></i> Billing & Profit
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">All Editable</span>
                </div>
                <div class="card-body p-4">
                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Repair Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-lg fw-semibold" id="repairStatusSelect">
                            <option value="Received" {{ $repair->status == 'Received' ? 'selected' : '' }}>Received</option>
                            <option value="In Progress" {{ $repair->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Waiting for Parts" {{ $repair->status == 'Waiting for Parts' ? 'selected' : '' }}>Waiting for Parts</option>
                            <option value="Ready" {{ $repair->status == 'Ready' ? 'selected' : '' }}>Ready (Fixed)</option>
                            <option value="Delivered" {{ $repair->status == 'Delivered' ? 'selected' : '' }}>Delivered to Customer</option>
                            <option value="Cancelled" {{ $repair->status == 'Cancelled' ? 'selected' : '' }}>Cancelled / Cannot Fix</option>
                        </select>
                    </div>

                    <hr class="my-3">

                    <!-- Editable Material Cost -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-danger small text-uppercase mb-0">
                                <i class="fa-solid fa-box text-danger me-1"></i> Material / Parts Cost (खर्च) (₹)
                            </label>
                            <small class="text-muted">Directly editable</small>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-danger fw-bold">₹</span>
                            <input type="number" step="0.01" name="material_cost" id="materialCostInput" class="form-control form-control-lg fw-bold text-danger" placeholder="0.00" value="{{ old('material_cost', $repair->material_cost > 0 ? $repair->material_cost : '') }}" min="0">
                        </div>
                        <small class="text-muted">Auto-filled from parts table above, or type directly</small>
                    </div>

                    <!-- Editable Labor / Service Charge -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted text-uppercase">Service / Labor Charge (मजदूरी) (₹)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white fw-bold">₹</span>
                            <input type="number" step="0.01" name="service_charge" id="serviceChargeInput" class="form-control form-control-lg fw-bold" placeholder="0.00" value="{{ old('service_charge', $repair->service_charge > 0 ? $repair->service_charge : '') }}" min="0">
                        </div>
                        <small class="text-muted">Labor or repairing fee charged to customer</small>
                    </div>

                    <!-- Editable Total Customer Charge -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold text-primary small text-uppercase mb-0">Total Customer Bill (Final Bill ₹) *</label>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" id="btnAutoCalcBill" title="Sum: Material Cost + Service Charge">
                                <i class="fa-solid fa-plus-minus me-1"></i> Auto-Sum
                            </button>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white fw-bold fs-5">₹</span>
                            @php
                                $currentBill = $repair->final_cost > 0 ? $repair->final_cost : ($repair->estimated_cost > 0 ? $repair->estimated_cost : '');
                            @endphp
                            <input type="number" step="0.01" name="final_cost" id="finalCostInput" class="form-control form-control-lg fw-bold border-primary text-primary fs-4" placeholder="0.00" value="{{ old('final_cost', $currentBill) }}" min="0" required>
                        </div>
                        <input type="hidden" name="estimated_cost" id="estimatedCostHidden" value="{{ $repair->estimated_cost }}">
                        <small class="text-muted">Total amount invoiced to customer (Editable anytime)</small>
                    </div>

                    <!-- Gross Profit Card Banner -->
                    <div class="alert alert-success border-success border-2 p-3 mb-4 rounded-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-uppercase fw-bold small text-success d-block">Estimated Net Profit (मुनाफा)</span>
                                <small class="text-muted">Total Bill - Material Cost</small>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold text-success mb-0" id="displayNetProfit">₹{{ number_format($repair->profit, 2) }}</h3>
                                @php
                                    $bill = $repair->final_cost > 0 ? $repair->final_cost : $repair->estimated_cost;
                                    $margin = $bill > 0 ? round(($repair->profit / $bill) * 100, 1) : 0;
                                @endphp
                                <small class="fw-semibold text-success" id="displayMarginBadge">{{ $margin }}% Profit Margin</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Editable Paid Amount -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-success small text-uppercase mb-0">
                                <i class="fa-solid fa-hand-holding-dollar text-success me-1"></i> Paid by Customer So Far (₹)
                            </label>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-success fw-bold">₹</span>
                            <input type="number" step="0.01" name="paid_amount" id="advancePaymentInput" class="form-control form-control-lg fw-bold border-success text-success" placeholder="0.00" value="{{ old('paid_amount', $repair->paid_amount > 0 ? $repair->paid_amount : '') }}" min="0">
                        </div>
                        <!-- Quick payment buttons -->
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" id="btnPaidFull">Full Paid</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnPaidHalf">50% Paid</button>
                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" id="btnPaidZero">Clear (₹0)</button>
                        </div>
                    </div>

                    <!-- Pending Customer Due (बाकी) -->
                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-dark">Pending Customer Due (बाकी):</span>
                            <span class="fs-5 fw-bold {{ $repair->due_amount > 0 ? 'text-danger' : 'text-success' }}" id="displayPendingDue">
                                {{ $repair->due_amount <= 0 && $repair->paid_amount > 0 ? '₹0.00 (Fully Paid)' : '₹' . number_format($repair->due_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold shadow-sm">
                        <i class="fa-solid fa-save me-2"></i> Update Repair Record
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Disable mouse-wheel scrolling changing number inputs
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('wheel', function(e) {
            e.preventDefault();
        }, { passive: false });
    });

    // Quick problem pills click handler
    const problemDescriptionInput = document.getElementById('problemDescriptionInput');
    document.querySelectorAll('.problem-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const prob = this.getAttribute('data-problem');
            const current = problemDescriptionInput.value.trim();
            if (!current) {
                problemDescriptionInput.value = prob;
            } else if (!current.includes(prob)) {
                problemDescriptionInput.value = current + ', ' + prob;
            }
            problemDescriptionInput.focus();
        });
    });

    const materialsContainer = document.getElementById('materialsContainer');
    const btnAddMaterial = document.getElementById('btnAddMaterial');
    const materialCostInput = document.getElementById('materialCostInput');
    const serviceChargeInput = document.getElementById('serviceChargeInput');
    const finalCostInput = document.getElementById('finalCostInput');
    const estimatedCostHidden = document.getElementById('estimatedCostHidden');
    const advancePaymentInput = document.getElementById('advancePaymentInput');

    const displayTableMaterialCost = document.getElementById('displayTableMaterialCost');
    const displayNetProfit = document.getElementById('displayNetProfit');
    const displayMarginBadge = document.getElementById('displayMarginBadge');
    const displayPendingDue = document.getElementById('displayPendingDue');

    // Add Material Row
    btnAddMaterial.addEventListener('click', function() {
        const firstRow = materialsContainer.querySelector('.material-row');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelector('.part-preset-select').value = '';
        newRow.querySelector('.part-product-id').value = '';
        newRow.querySelector('.part-name').value = '';
        newRow.querySelector('.part-qty').value = 1;
        newRow.querySelector('.part-cost').value = '';
        newRow.querySelector('.part-cost').placeholder = '0.00';
        newRow.querySelector('.part-subtotal').value = '';
        newRow.querySelector('.part-subtotal').placeholder = '0.00';

        materialsContainer.appendChild(newRow);
        attachRowEvents(newRow);
        syncMaterialsTableToInput();
    });

    function attachRowEvents(row) {
        const presetSelect = row.querySelector('.part-preset-select');
        const prodIdInput = row.querySelector('.part-product-id');
        const nameInput = row.querySelector('.part-name');
        const qtyInput = row.querySelector('.part-qty');
        const costInput = row.querySelector('.part-cost');
        const subtotalInput = row.querySelector('.part-subtotal');
        const btnRemove = row.querySelector('.btn-remove-row');

        // Prevent mouse wheel on number inputs inside row
        [qtyInput, costInput].forEach(inp => {
            if (inp) {
                inp.addEventListener('wheel', e => e.preventDefault(), { passive: false });
            }
        });

        // Preset inventory item selected
        presetSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (this.value) {
                nameInput.value = this.value;
                prodIdInput.value = selected.getAttribute('data-id') || '';
                const cost = parseFloat(selected.getAttribute('data-cost')) || 0;
                costInput.value = cost > 0 ? cost.toFixed(2) : '';
            }
            updateRowSubtotal();
        });

        function updateRowSubtotal() {
            const qty = parseInt(qtyInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;
            const subtotal = qty * cost;
            subtotalInput.value = subtotal > 0 ? '₹' + subtotal.toFixed(2) : '';
            syncMaterialsTableToInput();
        }

        qtyInput.addEventListener('input', updateRowSubtotal);
        costInput.addEventListener('input', updateRowSubtotal);
        nameInput.addEventListener('input', syncMaterialsTableToInput);

        btnRemove.addEventListener('click', function() {
            if (materialsContainer.querySelectorAll('.material-row').length > 1) {
                row.remove();
                syncMaterialsTableToInput();
            } else {
                // Clear row
                presetSelect.value = '';
                prodIdInput.value = '';
                nameInput.value = '';
                qtyInput.value = 1;
                costInput.value = '';
                subtotalInput.value = '';
                syncMaterialsTableToInput();
            }
        });
    }

    // Attach events to initial rows
    materialsContainer.querySelectorAll('.material-row').forEach(row => attachRowEvents(row));

    function syncMaterialsTableToInput() {
        let tableSum = 0;
        let hasItems = false;

        materialsContainer.querySelectorAll('.material-row').forEach(row => {
            const name = row.querySelector('.part-name').value.trim();
            const qty = parseInt(row.querySelector('.part-qty').value) || 0;
            const cost = parseFloat(row.querySelector('.part-cost').value) || 0;
            if (name !== '' || cost > 0) {
                hasItems = true;
                tableSum += (qty * cost);
            }
        });

        displayTableMaterialCost.textContent = '₹' + tableSum.toFixed(2);

        // If rows were actively edited and have items, update the materialCostInput
        if (hasItems && tableSum > 0) {
            materialCostInput.value = tableSum.toFixed(2);
        }

        calculateFinancials();
    }

    function calculateFinancials() {
        const materialCost = parseFloat(materialCostInput.value) || 0;
        const serviceCharge = parseFloat(serviceChargeInput.value) || 0;
        let finalCost = parseFloat(finalCostInput.value) || 0;

        estimatedCostHidden.value = (finalCost > 0 ? finalCost : (materialCost + serviceCharge)).toFixed(2);

        const paid = parseFloat(advancePaymentInput.value) || 0;
        const bill = finalCost > 0 ? finalCost : (materialCost + serviceCharge);
        const due = Math.max(0, bill - paid);
        const profit = Math.max(0, bill - materialCost);
        const margin = bill > 0 ? ((profit / bill) * 100).toFixed(1) : 0;

        displayPendingDue.textContent = '₹' + due.toFixed(2);
        if (due <= 0 && paid > 0) {
            displayPendingDue.className = 'fs-5 fw-bold text-success';
            displayPendingDue.textContent = '₹0.00 (Fully Paid)';
        } else {
            displayPendingDue.className = 'fs-5 fw-bold text-danger';
        }

        displayNetProfit.textContent = '₹' + profit.toFixed(2);
        displayMarginBadge.textContent = margin + '% Profit Margin';
    }

    // Auto-sum button
    document.getElementById('btnAutoCalcBill').addEventListener('click', function() {
        const mat = parseFloat(materialCostInput.value) || 0;
        const srv = parseFloat(serviceChargeInput.value) || 0;
        const sum = mat + srv;
        finalCostInput.value = sum > 0 ? sum.toFixed(2) : '';
        calculateFinancials();
    });

    finalCostInput.addEventListener('input', calculateFinancials);
    materialCostInput.addEventListener('input', calculateFinancials);
    serviceChargeInput.addEventListener('input', calculateFinancials);
    advancePaymentInput.addEventListener('input', calculateFinancials);

    // Quick payment buttons
    document.getElementById('btnPaidFull').addEventListener('click', function() {
        const total = parseFloat(finalCostInput.value) || (parseFloat(materialCostInput.value) || 0) + (parseFloat(serviceChargeInput.value) || 0);
        advancePaymentInput.value = total > 0 ? total.toFixed(2) : '';
        calculateFinancials();
    });

    document.getElementById('btnPaidHalf').addEventListener('click', function() {
        const total = parseFloat(finalCostInput.value) || (parseFloat(materialCostInput.value) || 0) + (parseFloat(serviceChargeInput.value) || 0);
        const half = total * 0.5;
        advancePaymentInput.value = half > 0 ? half.toFixed(2) : '';
        calculateFinancials();
    });

    document.getElementById('btnPaidZero').addEventListener('click', function() {
        advancePaymentInput.value = '';
        calculateFinancials();
    });

    calculateFinancials();
});
</script>
@endpush
