@extends('layouts.admin')

@section('content')
<div class="row mb-3">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold m-0"><i class="fa-solid fa-cash-register text-primary me-2"></i> POS Terminal</h2>
        <div>
            <span class="badge bg-dark fs-6 py-2 px-3"><i class="fa-solid fa-clock me-1 text-warning"></i> <span id="clock"></span></span>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .pos-product-card {
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.1s;
        border: 2px solid transparent;
    }
    .pos-product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        border-color: #0d6efd;
    }
    .cart-table th, .cart-table td {
        vertical-align: middle;
        padding: 0.5rem;
    }
    .products-grid-container {
        height: calc(100vh - 250px);
        overflow-y: auto;
        padding-right: 5px;
    }
    .cart-container {
        height: calc(100vh - 460px);
        overflow-y: auto;
    }
    /* Hide scrollbar for cleaner look */
    .products-grid-container::-webkit-scrollbar, .cart-container::-webkit-scrollbar {
        width: 6px;
    }
    .products-grid-container::-webkit-scrollbar-thumb, .cart-container::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }
    .pos-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .pos-summary-label {
        font-weight: 600;
        color: #495057;
    }
    .pos-summary-input {
        width: 120px;
        text-align: right;
    }
    /* Mobile App-Like Enhancements */
    @media (max-width: 991px) {
        .products-grid-container, .cart-container {
            height: auto;
            max-height: 50vh;
        }
        .col-lg-6 {
            margin-bottom: 20px;
        }
        /* Mobile Floating Cart Summary */
        .mobile-cart-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            padding: 15px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 1050;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        body {
            padding-bottom: 80px; /* Space for fixed bottom bar */
        }
    }
    @media (min-width: 992px) {
        .mobile-cart-summary {
            display: none;
        }
    }
</style>
@endpush

<form action="{{ route('admin.pos.store') }}" method="POST" id="posForm">
    @csrf
    <div class="row g-3">
        <!-- Left Side: Product Grid & Search -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white p-3 border-bottom">
                    <div class="input-group input-group-lg shadow-sm">
                        <span class="input-group-text bg-primary text-white border-primary"><i class="fa-solid fa-barcode"></i></span>
                        <input type="text" class="form-control border-primary" id="barcodeSearch" placeholder="Scan Barcode, SKU, or Search Product..." autofocus autocomplete="off">
                    </div>
                </div>
                <div class="card-body bg-light products-grid-container p-3">
                    <div class="row g-3" id="productsGrid">
                        @foreach($products as $product)
                            <div class="col-4 col-md-3 col-xl-3 product-item" 
                                 data-id="{{ $product->id }}" 
                                 data-name="{{ $product->name }}" 
                                 data-sku="{{ strtolower($product->sku ?? '') }}"
                                 data-barcode="{{ strtolower($product->barcode ?? '') }}"
                                 data-price="{{ $product->selling_price }}" 
                                 data-stock="{{ $product->stock_quantity }}"
                                 data-search="{{ strtolower($product->name . ' ' . ($product->sku ?? '') . ' ' . ($product->barcode ?? '') . ' ' . ($product->model_number ?? '')) }}">
                                <div class="card h-100 pos-product-card shadow-sm border-0 rounded-3 overflow-hidden" onclick="addToCart({{ $product->id }})">
                                    <div class="bg-white d-flex align-items-center justify-content-center p-2 border-bottom" style="height: 100px;">
                                        @if($product->mainImage)
                                            @php $img = $product->mainImage->image; @endphp
                                            <img src="{{ str_starts_with($img, 'http') ? $img : Storage::url($img) }}" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                        @else
                                            <i class="fa-solid fa-box text-muted fs-1"></i>
                                        @endif
                                    </div>
                                    <div class="card-body p-2 text-center d-flex flex-column justify-content-between">
                                        <div class="fw-bold small text-truncate mb-0" title="{{ $product->name }}">{{ $product->name }}</div>
                                        @if($product->sku)
                                            <small class="text-muted font-monospace" style="font-size: 0.72rem;">SKU: {{ $product->sku }}</small>
                                        @endif
                                        <div class="text-primary fw-bold">₹{{ number_format($product->selling_price, 2) }}</div>
                                        <div class="badge bg-{{ $product->stock_quantity > 5 ? 'success' : 'warning' }} mt-1">Stock: {{ $product->stock_quantity }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="noProductsFound" class="text-center py-5 d-none">
                        <i class="fa-solid fa-magnifying-glass fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No products found.</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Cart & Checkout -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100 d-flex flex-column">
                
                <!-- Customer Selection -->
                <div class="card-header bg-white p-3 border-bottom">
                    <div class="d-flex gap-2">
                        <select name="customer_id" id="customer_id" class="form-select select2-customer w-100">
                            <option value="">-- Walk-in Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->phone }} - {{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="Add Customer">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Cart Table -->
                <div class="card-body p-0 cart-container bg-white">
                    <table class="table table-hover cart-table mb-0 w-100" id="cartTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="45%">Item</th>
                                <th width="20%">Price</th>
                                <th width="20%" class="text-center">Qty</th>
                                <th width="10%" class="text-end">Total</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody id="cartItems">
                            <!-- Items injected via JS -->
                        </tbody>
                    </table>
                    <div id="emptyCartMessage" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-cart-arrow-down fs-1 mb-2"></i>
                        <p class="mb-0">Cart is empty</p>
                    </div>
                </div>

                <!-- Totals & Payment -->
                <div class="card-footer bg-white p-3 border-top">
                    
                    <div class="pos-summary-row">
                        <span class="pos-summary-label">Subtotal (₹)</span>
                        <input type="number" step="0.01" name="subtotal" id="finalSubtotal" class="form-control form-control-sm pos-summary-input bg-light border-0" readonly value="0">
                    </div>
                    
                    <div class="pos-summary-row">
                        <span class="pos-summary-label">Discount (₹)</span>
                        <input type="number" step="0.01" name="discount" id="discountInput" class="form-control form-control-sm pos-summary-input" value="0">
                    </div>
                    
                    <div class="pos-summary-row" id="afterDiscountRow" style="display:none !important;">
                        <span class="pos-summary-label text-success">After Discount</span>
                        <span class="text-end text-success fw-bold" id="afterDiscountVal">₹0.00</span>
                    </div>
                    
                    <div class="pos-summary-row border-bottom pb-2 mb-2">
                        <span class="pos-summary-label">Tax (₹)</span>
                        <input type="number" step="0.01" name="tax" id="taxInput" class="form-control form-control-sm pos-summary-input" value="0">
                    </div>
                    
                    <div class="pos-summary-row bg-light p-2 rounded mb-3">
                        <span class="pos-summary-label fs-5 text-primary">Total Payable</span>
                        <input type="number" step="0.01" name="grand_total" id="grandTotal" class="form-control form-control-lg pos-summary-input bg-transparent border-0 fw-bold text-primary fs-4 p-0" readonly value="0">
                    </div>

                    <!-- Editable Paid Amount Received & Customer Payment Mode -->
                    <div class="mb-3 p-3 bg-white rounded-3 border shadow-sm">
                        <!-- Mode Selector & Header -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0 text-dark">
                                <i class="fa-solid fa-money-bill-wave text-success me-1"></i> Paid Amount Received (₹)
                            </label>
                            <span id="modeStatusBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle py-1 px-2 fw-semibold">
                                <i class="fa-solid fa-bolt me-1"></i> Auto Full
                            </span>
                        </div>

                        <!-- Full (Auto) vs Manual Mode Toggle Buttons -->
                        <div class="d-flex gap-1 mb-2">
                            <button type="button" class="btn btn-sm btn-primary active flex-fill fw-semibold py-1" id="btnModeAuto" title="Automatically match paid amount to Grand Total">
                                <i class="fa-solid fa-bolt me-1"></i> Full (Auto)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill fw-semibold py-1" id="btnModeManual" title="Manually type what customer paid">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Manual Enter
                            </button>
                        </div>

                        <!-- Paid Amount Input Box with Clear / Backspace button -->
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light fw-bold text-dark fs-5">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="paid_amount" 
                                   id="paidAmount" 
                                   class="form-control form-control-lg fw-bold text-dark number-only fs-4" 
                                   placeholder="0.00" 
                                   value="0"
                                   autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary px-3" id="btnClearPaid" title="Clear amount to type manually">
                                <i class="fa-solid fa-eraser"></i> Clear
                            </button>
                        </div>

                        <!-- Quick Cash Denominations / Preset Chips -->
                        <div class="mb-3">
                            <div class="small text-muted mb-1 fw-semibold">Quick Amount Presets:</div>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2 fw-semibold quick-amt-btn" data-amt="full">Full</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 quick-amt-btn" data-amt="100">₹100</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 quick-amt-btn" data-amt="200">₹200</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 quick-amt-btn" data-amt="500">₹500</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 quick-amt-btn" data-amt="1000">₹1,000</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 quick-amt-btn" data-amt="2000">₹2,000</button>
                                <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2 quick-amt-btn" data-amt="half">50%</button>
                                <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 quick-amt-btn" data-amt="zero">Unpaid</button>
                            </div>
                        </div>
                        
                        <!-- Real-time Balance / Change & Status Display -->
                        <div class="p-2 rounded-2 border bg-light mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold text-muted" id="balanceLabel">Balance Due</span>
                                <span class="badge bg-success fs-6" id="paymentStatusBadge">Paid in Full</span>
                            </div>
                            <div class="mt-1 d-flex justify-content-between align-items-baseline">
                                <span class="fs-4 fw-bold text-muted" id="balanceAmountDisplay">₹0.00</span>
                                <input type="hidden" name="due_amount" id="dueAmount" value="0">
                            </div>
                        </div>
                        
                        <div id="dueWarningNotice" class="alert alert-warning py-1 px-2 small mb-0 d-none">
                            <i class="fa-solid fa-triangle-exclamation me-1 text-danger"></i>
                            <span>Pending customer due! Please select or add a Customer above to track this balance.</span>
                        </div>
                    </div>
                    
                    <!-- How Customer Paid: Payment Method & Notes -->
                    <div class="mb-3 p-3 bg-light rounded-3 border">
                        <label class="form-label pos-summary-label mb-1">
                            <i class="fa-solid fa-credit-card me-1 text-primary"></i> Payment Method (How Customer Paid)
                        </label>
                        <select name="payment_method" id="paymentMethodSelect" class="form-select form-select-lg mb-2">
                            <option value="Cash" selected>💵 Cash</option>
                            <option value="UPI">📱 UPI / QR (PhonePe / GPay / Paytm)</option>
                            <option value="Card">💳 Debit / Credit Card</option>
                            <option value="Bank Transfer">🏦 Bank Transfer / NEFT</option>
                            <option value="Split">🔄 Split (Cash + UPI / Card)</option>
                            <option value="Credit / Khata">📑 Customer Credit / Khata (Due)</option>
                        </select>
                        
                        <!-- Manual Entry Notes / Ref ID -->
                        <div>
                            <label class="form-label small text-muted mb-1">
                                <i class="fa-solid fa-pen me-1"></i> Payment Notes / Reference ID (Optional)
                            </label>
                            <input type="text" 
                                   name="notes" 
                                   id="paymentNotes" 
                                   class="form-control form-control-sm" 
                                   placeholder="e.g. UPI Ref / UTR No / Split note">
                        </div>
                    </div>

                    <button type="button" id="submitBtn" class="btn btn-primary w-100 py-2 fs-5 fw-bold">
                        <i class="fa-solid fa-check-circle me-1"></i> Pay & Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Fixed Cart Summary -->
    <div class="mobile-cart-summary">
        <div>
            <div class="small text-muted mb-0">Total Payable</div>
            <div class="fs-4 fw-bold text-primary" id="mobileGrandTotal">₹0.00</div>
        </div>
        <button type="button" class="btn btn-primary px-4 fw-bold" onclick="document.getElementById('submitBtn').click()">
            <i class="fa-solid fa-check-circle me-1"></i> Checkout
        </button>
    </div>
</form>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addCustomerForm" action="{{ route('admin.customers.store') }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
             <div class="alert alert-danger d-none" id="customerErrors"></div>
             <div class="mb-3">
                <label for="customerName" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="customerName" name="name" required>
             </div>
             <div class="mb-3">
                <label for="customerPhone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="customerPhone" name="phone">
             </div>
             <input type="hidden" name="status" value="1">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="saveCustomerBtn">Save Customer</button>
          </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Global Cart Object
    let cart = {};
    let isManualPaid = false;

    function setPaymentMode(manual) {
        isManualPaid = manual;
        if (isManualPaid) {
            $('#btnModeManual').addClass('btn-primary active text-white').removeClass('btn-outline-secondary');
            $('#btnModeAuto').removeClass('btn-primary active text-white').addClass('btn-outline-secondary');
            $('#modeStatusBadge')
                .removeClass('bg-primary-subtle text-primary border-primary-subtle')
                .addClass('bg-warning-subtle text-dark border-warning-subtle')
                .html('<i class="fa-solid fa-pen-to-square me-1 text-warning-emphasis"></i> Manual Entry');
        } else {
            $('#btnModeAuto').addClass('btn-primary active text-white').removeClass('btn-outline-secondary');
            $('#btnModeManual').removeClass('btn-primary active text-white').addClass('btn-outline-secondary');
            $('#modeStatusBadge')
                .removeClass('bg-warning-subtle text-dark border-warning-subtle')
                .addClass('bg-primary-subtle text-primary border-primary-subtle')
                .html('<i class="fa-solid fa-bolt me-1"></i> Auto Full');
            let gt = parseFloat($('#grandTotal').val()) || 0;
            $('#paidAmount').val(gt > 0 ? gt.toFixed(2) : '0');
        }
    }

    function updateClock() {
        var now = new Date();
        $('#clock').text(now.toLocaleTimeString());
    }
    setInterval(updateClock, 1000);
    updateClock();

    $(document).ready(function() {
        $('.select2-customer').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Walk-in Customer --',
            allowClear: true
        });

        // Search Bar functionality (Filter Grid & SKU / Barcode Auto Add)
        $('#barcodeSearch').on('input', function() {
            let val = $(this).val().toLowerCase().trim();
            let hasResults = false;
            
            $('.product-item').each(function() {
                let searchTxt = $(this).data('search');
                let barcode = $(this).data('barcode') ? $(this).data('barcode').toString().toLowerCase() : '';
                let sku = $(this).data('sku') ? $(this).data('sku').toString().toLowerCase() : '';
                
                // Exact Barcode or Exact SKU Match triggers instant add to cart
                if (val !== '' && (barcode === val || sku === val)) {
                    addToCart($(this).data('id'));
                    $(this).show();
                    hasResults = true;
                    // Reset search after adding by barcode/sku
                    $('#barcodeSearch').val('');
                    $('#barcodeSearch').trigger('input'); 
                    return false; // Break each
                }
                
                if (searchTxt.includes(val) || barcode.includes(val) || sku.includes(val)) {
                    $(this).show();
                    hasResults = true;
                } else {
                    $(this).hide();
                }
            });
            
            if(!hasResults && val !== '') {
                $('#noProductsFound').removeClass('d-none');
            } else {
                $('#noProductsFound').addClass('d-none');
            }
        });

        // Barcode Gun Scanner Enter Key:
        // Gun scanners send Enter (13) at the end of a scan.
        $('#barcodeSearch').on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                let val = $(this).val().toLowerCase().trim();
                if (!val) return;
                
                let foundId = null;
                $('.product-item').each(function() {
                    let barcode = $(this).data('barcode') ? $(this).data('barcode').toString().toLowerCase() : '';
                    let sku = $(this).data('sku') ? $(this).data('sku').toString().toLowerCase() : '';
                    if (barcode === val || sku === val) {
                        foundId = $(this).data('id');
                        return false;
                    }
                });
                
                if (!foundId) {
                    let visible = $('.product-item:visible');
                    if (visible.length === 1) {
                        foundId = visible.first().data('id');
                    }
                }
                
                if (foundId) {
                    addToCart(foundId);
                    $(this).val('').trigger('input');
                }
            }
        });

        // Form Submit
        $('#submitBtn').click(function() {
            if(Object.keys(cart).length === 0) {
                alert('Cart is empty!');
                return;
            }
            
            // If cashier left paid amount blank, treat as 0
            let paidInput = $('#paidAmount');
            if (paidInput.val().trim() === '') {
                paidInput.val('0');
                updateTotalsUI();
            }

            let due = parseFloat($('#dueAmount').val()) || 0;
            let customerId = $('.select2-customer').val();
            if (due > 0 && !customerId) {
                if (!confirm('This sale has a pending balance of ₹' + due.toFixed(2) + ' but no customer is selected. Do you want to proceed as walk-in credit, or cancel to select a customer? Click OK to proceed anyway.')) {
                    return;
                }
            }

            if(!$('#posForm')[0].checkValidity()) {
                $('#posForm')[0].reportValidity();
                return;
            }
            $('#posForm').submit();
        });

        // Mode Toggles (Auto vs Manual)
        $('#btnModeAuto').on('click', function() {
            setPaymentMode(false);
            updateTotalsUI();
        });

        $('#btnModeManual').on('click', function() {
            setPaymentMode(true);
            $('#paidAmount').focus().select();
        });

        $('#btnClearPaid').on('click', function() {
            setPaymentMode(true);
            $('#paidAmount').val('').focus();
            updateTotalsUI();
        });

        // Paid Amount interaction: selecting or typing in box switches to Manual mode cleanly
        $('#paidAmount').on('focus', function() {
            setPaymentMode(true);
            $(this).select();
        });

        $('#paidAmount').on('input', function() {
            setPaymentMode(true);
            updateTotalsUI();
        });

        $('#paidAmount').on('blur', function() {
            let val = $(this).val().trim();
            if (val === '') {
                $(this).val('0');
                updateTotalsUI();
            }
        });

        // Quick Payment Preset Buttons
        $('.quick-amt-btn').on('click', function() {
            let amt = $(this).data('amt');
            let gt = parseFloat($('#grandTotal').val()) || 0;
            setPaymentMode(true);

            if (amt === 'full') {
                $('#paidAmount').val(gt > 0 ? gt.toFixed(2) : '0');
            } else if (amt === 'half') {
                $('#paidAmount').val((gt / 2).toFixed(2));
            } else if (amt === 'zero') {
                $('#paidAmount').val('0');
            } else {
                let num = parseFloat(amt);
                if (!isNaN(num)) {
                    $('#paidAmount').val(num.toFixed(2));
                }
            }
            updateTotalsUI();
        });

        // Payment Method change updates note placeholder
        $('#paymentMethodSelect').on('change', function() {
            let method = $(this).val();
            let notesInput = $('#paymentNotes');
            if (method === 'UPI') {
                notesInput.attr('placeholder', 'Enter UPI UTR / Transaction ID (e.g. 40291...)');
            } else if (method === 'Split') {
                notesInput.attr('placeholder', 'e.g. 500 Cash + 500 PhonePe');
            } else if (method === 'Credit / Khata') {
                notesInput.attr('placeholder', 'Reason for credit / promised payment date');
            } else if (method === 'Card') {
                notesInput.attr('placeholder', 'Card last 4 digits / Approval code');
            } else {
                notesInput.attr('placeholder', 'e.g. UPI Ref / UTR No / Split note');
            }
        });

        // Listen for discount/tax changes
        $(document).on('input', '#discountInput, #taxInput', function() {
            updateTotalsUI();
        });

        $('.select2-customer').on('change', function() {
            let due = parseFloat($('#dueAmount').val()) || 0;
            if (due > 0 && !$(this).val()) {
                $('#dueWarningNotice').removeClass('d-none');
            } else {
                $('#dueWarningNotice').addClass('d-none');
            }
        });

        // Add Customer AJAX Submission
        $('#addCustomerForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#saveCustomerBtn');
            let url = form.attr('action');
            let data = form.serialize();
            let errorsDiv = $('#customerErrors');
            
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
            errorsDiv.addClass('d-none').html('');

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if(response.success) {
                        let customer = response.customer;
                        let optionText = (customer.phone ? customer.phone + ' - ' : '') + customer.name;
                        let newOption = new Option(optionText, customer.id, true, true);
                        $('.select2-customer').append(newOption).trigger('change');
                        
                        $('#addCustomerModal').modal('hide');
                        form[0].reset();
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorHtml = '<ul class="mb-0">';
                        for(let key in errors) {
                            errorHtml += '<li>' + errors[key][0] + '</li>';
                        }
                        errorHtml += '</ul>';
                        errorsDiv.html(errorHtml).removeClass('d-none');
                    } else {
                        alert('An error occurred while saving.');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).html('Save Customer');
                }
            });
        });
    });

    // Add product to cart
    function addToCart(productId) {
        let el = $('.product-item[data-id="' + productId + '"]');
        let name = el.data('name');
        let price = parseFloat(el.data('price'));
        let maxStock = parseInt(el.data('stock'));

        if(maxStock <= 0) {
            alert('Product out of stock!');
            return;
        }

        if(cart[productId]) {
            if(cart[productId].qty < maxStock) {
                cart[productId].qty++;
            } else {
                alert('Maximum stock reached!');
            }
        } else {
            cart[productId] = {
                name: name,
                price: price,
                qty: 1,
                max: maxStock
            };
        }
        renderCart();
    }

    // Change qty via input
    function changeQty(productId, input) {
        let val = parseInt($(input).val());
        if(isNaN(val) || val < 1) val = 1;
        
        let max = cart[productId].max;
        if(val > max) {
            alert('Maximum stock is ' + max);
            val = max;
        }
        
        cart[productId].qty = val;
        renderCart();
    }

    // Change qty via buttons
    function increaseQty(productId) {
        if(cart[productId].qty < cart[productId].max) {
            cart[productId].qty++;
            renderCart();
        } else {
            alert('Maximum stock reached!');
        }
    }

    function decreaseQty(productId) {
        if(cart[productId].qty > 1) {
            cart[productId].qty--;
            renderCart();
        }
    }

    function removeFromCart(productId) {
        delete cart[productId];
        renderCart();
    }

    function renderCart() {
        let html = '';
        let subtotal = 0;
        
        if(Object.keys(cart).length === 0) {
            $('#cartItems').html('');
            $('#emptyCartMessage').show();
            updateTotalsUI(0);
            return;
        }
        
        $('#emptyCartMessage').hide();

        for (let id in cart) {
            let item = cart[id];
            let itemTotal = item.price * item.qty;
            subtotal += itemTotal;

            html += `
                <tr>
                    <td>
                        <div class="fw-bold small text-truncate" style="max-width: 150px;" title="${item.name}">${item.name}</div>
                        <input type="hidden" name="products[]" value="${id}">
                        <input type="hidden" name="prices[]" value="${item.price}">
                    </td>
                    <td>₹${item.price.toFixed(2)}</td>
                    <td>
                        <div class="input-group input-group-sm flex-nowrap" style="width: 90px;">
                            <button type="button" class="btn btn-outline-secondary px-2" onclick="decreaseQty(${id})"><i class="fa-solid fa-minus" style="font-size: 0.6rem;"></i></button>
                            <input type="number" name="quantities[]" class="form-control text-center p-0 fw-bold" value="${item.qty}" min="1" max="${item.max}" onchange="changeQty(${id}, this)">
                            <button type="button" class="btn btn-outline-secondary px-2" onclick="increaseQty(${id})"><i class="fa-solid fa-plus" style="font-size: 0.6rem;"></i></button>
                        </div>
                    </td>
                    <td class="text-end fw-bold">₹${itemTotal.toFixed(2)}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="removeFromCart(${id})"><i class="fa-solid fa-xmark"></i></button>
                    </td>
                </tr>
            `;
        }
        
        $('#cartItems').html(html);
        updateTotalsUI(subtotal);
    }

    function updateTotalsUI(calculatedSubtotal = null) {
        let subtotal = calculatedSubtotal !== null ? calculatedSubtotal : parseFloat($('#finalSubtotal').val()) || 0;
        
        $('#finalSubtotal').val(subtotal.toFixed(2));

        let discount = parseFloat($('#discountInput').val()) || 0;
        if(discount > 0) {
            $('#afterDiscountRow').show();
            $('#afterDiscountVal').text('₹' + (subtotal - discount).toFixed(2));
        } else {
            $('#afterDiscountRow').hide();
        }

        let tax = parseFloat($('#taxInput').val()) || 0;
        
        let grandTotal = (subtotal - discount) + tax;
        if(grandTotal < 0) grandTotal = 0;
        
        $('#grandTotal').val(grandTotal.toFixed(2));
        $('#mobileGrandTotal').text('₹' + grandTotal.toFixed(2));

        let paidInput = $('#paidAmount');

        // Only auto-sync with grandTotal if in Auto Full mode
        if (!isManualPaid) {
            paidInput.val(grandTotal > 0 ? grandTotal.toFixed(2) : '0');
        }

        // Numerical calculation:
        // If cashier cleared the text box to type, treat as 0 for live math without overwriting their input text!
        let rawPaid = paidInput.val().trim();
        let paid = rawPaid === '' ? 0 : (parseFloat(rawPaid) || 0);

        let due = grandTotal - paid;
        let change = 0;

        if (due > 0.001) {
            // Customer owes balance (Due)
            $('#balanceLabel').html('<i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> Balance Due (Customer Owes)');
            $('#balanceAmountDisplay').text('₹' + due.toFixed(2)).removeClass('text-success text-muted').addClass('text-danger');
            $('#dueAmount').val(due.toFixed(2));
            if (paid > 0) {
                $('#paymentStatusBadge').removeClass('bg-success bg-danger text-white').addClass('bg-warning text-dark').text('Partial Paid');
            } else {
                $('#paymentStatusBadge').removeClass('bg-success bg-warning text-dark').addClass('bg-danger text-white').text('Unpaid / Credit');
            }
            if (!$('.select2-customer').val()) {
                $('#dueWarningNotice').removeClass('d-none');
            } else {
                $('#dueWarningNotice').addClass('d-none');
            }
        } else if (paid > grandTotal + 0.001) {
            // Customer paid extra note (Change Return)
            change = paid - grandTotal;
            $('#dueAmount').val('0.00');
            $('#balanceLabel').html('<i class="fa-solid fa-hand-holding-dollar text-success me-1"></i> Change to Return to Customer');
            $('#balanceAmountDisplay').text('₹' + change.toFixed(2)).removeClass('text-danger text-muted').addClass('text-success');
            $('#paymentStatusBadge').removeClass('bg-warning bg-danger text-dark').addClass('bg-success text-white').text('Paid in Full');
            $('#dueWarningNotice').addClass('d-none');
        } else {
            // Paid in full
            $('#dueAmount').val('0.00');
            $('#balanceLabel').html('<i class="fa-solid fa-check-circle text-success me-1"></i> Balance Due');
            $('#balanceAmountDisplay').text('₹0.00').removeClass('text-danger text-success').addClass('text-muted');
            $('#paymentStatusBadge').removeClass('bg-warning bg-danger text-dark').addClass('bg-success text-white').text('Paid in Full');
            $('#dueWarningNotice').addClass('d-none');
        }
    }
</script>
@endpush
@endsection


