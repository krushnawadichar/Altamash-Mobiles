@extends('layouts.admin')

@section('title', 'Create Purchase Order')
@section('page_title', 'New Purchase & Stock Intake')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Record Purchase Order</h4>
            <small class="text-muted">Stock intake for smartphones with IMEIs and accessories with automatic inventory ledger update</small>
        </div>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Purchases
        </a>
    </div>

    <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        <div class="row g-3">

            <!-- Left Col: Supplier & Products -->
            <div class="col-lg-8">
                <!-- Supplier Info -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-truck me-1 text-primary"></i>Supplier & Invoice Details</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fw-semibold small">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select select2" required>
                                    <option value="">Choose Supplier...</option>
                                    @foreach($suppliers as $sup)
                                        <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->name }} ({{ $sup->company_name ?: 'Distributor' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">Supplier Invoice No</label>
                                <input type="text" name="supplier_invoice_no" class="form-control" placeholder="e.g. INV-8899" value="{{ old('supplier_invoice_no') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Cart/Items -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="bi bi-boxes me-1 text-primary"></i>Purchase Items</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addProductRowBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add Product
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div id="itemsContainer">
                            <!-- Dynamically added product rows -->
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <label class="form-label fw-semibold small">Purchase Notes / Remarks</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Delivery notes, courier tracking, terms..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Right Col: Financial Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3 sticky-top" style="top: 80px;">
                    <div class="card-header bg-white">
                        <span class="fw-bold"><i class="bi bi-calculator me-1 text-success"></i>Bill Summary</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold" id="lblSubtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax Amount:</span>
                            <span class="fw-semibold" id="lblTax">₹0.00</span>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Discount (₹):</span>
                                <input type="number" step="0.01" name="discount_amount" id="discountInput" class="form-control form-control-sm text-end w-50" value="0.00">
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold fs-5">Grand Total:</span>
                            <span class="fw-bold fs-5 text-primary" id="lblGrandTotal">₹0.00</span>
                        </div>

                        <!-- Payment Breakdown -->
                        <div class="bg-light p-3 rounded-3 mb-3 border">
                            <div class="mb-2">
                                <label class="form-label fw-semibold small">Paid Amount (₹)</label>
                                <input type="number" step="0.01" name="paid_amount" id="paidInput" class="form-control fw-bold text-success" value="0.00">
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small">Payment Method</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="bank_transfer" selected>Bank Transfer (RTGS/NEFT)</option>
                                    <option value="upi">UPI / QR Code</option>
                                    <option value="cash">Cash</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold small">Payment Reference / UTR</label>
                                <input type="text" name="payment_reference" class="form-control form-control-sm" placeholder="Ref/Transaction #">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold text-danger">Due Amount:</span>
                            <span class="fw-bold fs-5 text-danger" id="lblDue">₹0.00</span>
                        </div>

                        <input type="hidden" name="subtotal" id="hiddenSubtotal" value="0">
                        <input type="hidden" name="tax_amount" id="hiddenTax" value="0">
                        <input type="hidden" name="grand_total" id="hiddenGrandTotal" value="0">

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> Save Purchase Order
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    const availableProducts = @json($products);
    let itemIndex = 0;

    function addRow() {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'border rounded-3 p-3 mb-3 bg-light item-card';
        row.id = `item-card-${itemIndex}`;
        row.dataset.index = itemIndex;

        let options = '<option value="">Select Product...</option>';
        availableProducts.forEach(p => {
            options += `<option value="${p.id}" data-type="${p.type}" data-price="${p.purchase_price}" data-tax="${p.tax_percent}">${p.name} (${p.sku}) [${p.type}]</option>`;
        });

        row.innerHTML = `
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Product <span class="text-danger">*</span></label>
                    <select name="items[${itemIndex}][product_id]" class="form-select product-select select2" required>
                        ${options}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty" value="1" min="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Cost Price (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="items[${itemIndex}][purchase_price]" class="form-control item-price" value="0.00" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Subtotal (₹)</label>
                    <input type="text" class="form-control-plaintext fw-bold text-primary item-subtotal" readonly value="₹0.00">
                    <input type="hidden" name="items[${itemIndex}][subtotal]" class="hidden-item-subtotal" value="0">
                    <input type="hidden" name="items[${itemIndex}][tax_percent]" class="item-tax-percent" value="0">
                </div>
                <div class="col-md-1 text-end">
                    <label class="form-label small d-block">&nbsp;</label>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeRow(${itemIndex})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Dynamic IMEI Container for Mobiles -->
            <div class="imei-inputs-wrapper mt-3 pt-2 border-top" style="display: none;" id="imei-wrapper-${itemIndex}">
                <div class="fw-bold small text-primary mb-2"><i class="bi bi-upc-scan me-1"></i>Mobile Phone Tracking: Individual IMEIs & Serials</div>
                <div class="imei-slots-container row g-2"></div>
            </div>
        `;

        container.appendChild(row);
        $(row).find('.select2').select2({ theme: 'bootstrap-5' });

        attachListeners(row, itemIndex);
        itemIndex++;
        recalculateTotals();
    }

    function removeRow(idx) {
        const row = document.getElementById(`item-card-${idx}`);
        if (row) {
            row.remove();
            recalculateTotals();
        }
    }

    function attachListeners(row, idx) {
        const prodSelect = row.querySelector('.product-select');
        const qtyInput = row.querySelector('.item-qty');
        const priceInput = row.querySelector('.item-price');
        const imeiWrapper = row.querySelector(`#imei-wrapper-${idx}`);
        const slotsContainer = row.querySelector('.imei-slots-container');

        $(prodSelect).on('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const defaultPrice = selectedOpt.dataset.price || 0;
                const defaultTax = selectedOpt.dataset.tax || 0;
                priceInput.value = parseFloat(defaultPrice).toFixed(2);
                row.querySelector('.item-tax-percent').value = defaultTax;
                renderImeiSlots();
                recalculateRow(row);
            }
        });

        qtyInput.addEventListener('input', function() {
            renderImeiSlots();
            recalculateRow(row);
        });

        priceInput.addEventListener('input', function() {
            recalculateRow(row);
        });

        function renderImeiSlots() {
            const selectedOpt = prodSelect.options[prodSelect.selectedIndex];
            if (!selectedOpt || selectedOpt.dataset.type !== 'mobile') {
                imeiWrapper.style.display = 'none';
                slotsContainer.innerHTML = '';
                return;
            }

            imeiWrapper.style.display = 'block';
            const qty = parseInt(qtyInput.value) || 0;
            let html = '';

            for (let i = 0; i < qty; i++) {
                html += `
                    <div class="col-md-4 mb-2">
                        <div class="bg-white p-2 border rounded shadow-none">
                            <span class="badge bg-primary mb-1">Unit #${i + 1}</span>
                            <input type="text" name="items[${idx}][serials][${i}][imei_1]" class="form-control form-control-sm mb-1 font-monospace" placeholder="IMEI 1 (15 digits) *" required>
                            <input type="text" name="items[${idx}][serials][${i}][imei_2]" class="form-control form-control-sm mb-1 font-monospace" placeholder="IMEI 2 (Optional)">
                            <div class="row g-1">
                                <div class="col-6"><input type="text" name="items[${idx}][serials][${i}][color]" class="form-control form-control-sm" placeholder="Color"></div>
                                <div class="col-6"><input type="text" name="items[${idx}][serials][${i}][storage]" class="form-control form-control-sm" placeholder="Storage"></div>
                            </div>
                        </div>
                    </div>
                `;
            }
            slotsContainer.innerHTML = html;
        }
    }

    function recalculateRow(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const subtotal = qty * price;

        row.querySelector('.item-subtotal').value = '₹' + subtotal.toFixed(2);
        row.querySelector('.hidden-item-subtotal').value = subtotal.toFixed(2);

        recalculateTotals();
    }

    function recalculateTotals() {
        let totalSub = 0;
        let totalTax = 0;

        document.querySelectorAll('.item-card').forEach(row => {
            const sub = parseFloat(row.querySelector('.hidden-item-subtotal')?.value) || 0;
            const taxP = parseFloat(row.querySelector('.item-tax-percent')?.value) || 0;
            totalSub += sub;
            totalTax += (sub * taxP) / 100;
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const grandTotal = Math.max(0, totalSub + totalTax - discount);
        const paid = parseFloat(document.getElementById('paidInput').value) || 0;
        const due = Math.max(0, grandTotal - paid);

        document.getElementById('lblSubtotal').innerText = '₹' + totalSub.toFixed(2);
        document.getElementById('lblTax').innerText = '₹' + totalTax.toFixed(2);
        document.getElementById('lblGrandTotal').innerText = '₹' + grandTotal.toFixed(2);
        document.getElementById('lblDue').innerText = '₹' + due.toFixed(2);

        document.getElementById('hiddenSubtotal').value = totalSub.toFixed(2);
        document.getElementById('hiddenTax').value = totalTax.toFixed(2);
        document.getElementById('hiddenGrandTotal').value = grandTotal.toFixed(2);
    }

    document.getElementById('addProductRowBtn').addEventListener('click', addRow);
    document.getElementById('discountInput').addEventListener('input', recalculateTotals);
    document.getElementById('paidInput').addEventListener('input', recalculateTotals);

    document.addEventListener('DOMContentLoaded', function() {
        addRow(); // add first row by default
    });
</script>
@endpush
