@extends('layouts.admin')

@section('title', 'Point of Sale (POS)')
@section('page_title', 'POS Billing Terminal')

@section('content')
<style>
    /* POS Terminal Specific Styling */
    .pos-layout {
        display: flex;
        gap: 1.25rem;
        height: calc(100vh - 120px);
    }

    .pos-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .pos-right {
        width: 440px;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--card-border);
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        overflow-y: auto;
        padding-right: 4px;
        flex: 1;
    }

    .product-card {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        user-select: none;
        position: relative;
    }

    .product-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(79, 70, 229, 0.12);
    }

    .product-card:active {
        transform: scale(0.98);
    }

    .product-card .img-box {
        height: 80px;
        background: #f8fafc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        overflow: hidden;
    }

    .product-card .img-box img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }

    .cart-items-container {
        flex: 1;
        overflow-y: auto;
        padding: 10px 14px;
    }

    .cart-item-row {
        border-bottom: 1px dashed #e2e8f0;
        padding: 8px 0;
    }

    .cart-item-row:last-child {
        border-bottom: none;
    }

    .pos-calc-area {
        background: #f8fafc;
        border-top: 1px solid var(--card-border);
        padding: 14px 18px;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
    }

    .category-pills {
        display: flex;
        gap: 6px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .category-pill {
        padding: 5px 12px;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .category-pill.active, .category-pill:hover {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    @media (max-width: 1200px) {
        .pos-layout {
            flex-direction: column;
            height: auto;
        }
        .pos-right {
            width: 100%;
        }
    }
</style>

<div class="pos-layout">

    <!-- Left Side: Product Search, Scanner & Catalog -->
    <div class="pos-left">
        <!-- Top Search Bar -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-2 d-flex gap-2 align-items-center">
                <div class="input-group input-group-lg flex-fill">
                    <span class="input-group-text bg-white border-end-0 text-primary">
                        <i class="bi bi-upc-scan fs-4"></i>
                    </span>
                    <input type="text" id="posSearchInput" class="form-control border-start-0 ps-0" placeholder="Scan Barcode / IMEI or Type Product Name (F4)..." autofocus>
                    <button class="btn btn-light border" type="button" id="clearSearchBtn"><i class="bi bi-x-lg"></i></button>
                </div>
                <button class="btn btn-outline-primary d-flex align-items-center gap-1" id="quickAddCustomerBtn" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                    <i class="bi bi-person-plus"></i>
                    <span class="d-none d-sm-inline">New Customer</span>
                </button>
            </div>
        </div>

        <!-- Category Filter Pills -->
        <div class="category-pills">
            <button class="category-pill active" data-cat="">All Categories</button>
            @foreach($categories as $cat)
                <button class="category-pill" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        <!-- Product Cards Grid -->
        <div class="product-grid" id="productGrid">
            <!-- Loaded dynamically via AJAX -->
        </div>
    </div>

    <!-- Right Side: POS Billing Cart & Calculation -->
    <div class="pos-right">
        <!-- Cart Header: Customer Selector -->
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-light rounded-top">
            <div class="flex-fill me-2">
                <select id="posCustomerSelect" class="form-select form-select-sm select2">
                    <option value="">Walk-in Customer (General)</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-mobile="{{ $c->mobile }}">
                            {{ $c->name }} ({{ $c->mobile }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-outline-danger btn-sm py-1 px-2" id="clearCartBtn" title="Empty Cart">
                <i class="bi bi-trash"></i>
            </button>
        </div>

        <!-- Cart Items List -->
        <div class="cart-items-container" id="cartItemsContainer">
            <div class="text-center text-muted py-5" id="emptyCartMessage">
                <i class="bi bi-cart3 fs-1 d-block mb-2 text-secondary"></i>
                <div class="fw-semibold">Cart is currently empty</div>
                <small>Scan a barcode or click products to add</small>
            </div>
        </div>

        <!-- Calculation & Checkout Area -->
        <div class="pos-calc-area">
            <div class="d-flex justify-content-between mb-1 small text-muted">
                <span>Subtotal (<span id="cartItemsCount">0</span> items):</span>
                <span class="fw-bold text-dark" id="posSubtotal">₹0.00</span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-1 small text-muted">
                <span>Discount:</span>
                <div class="d-flex align-items-center gap-1" style="width: 130px;">
                    <input type="number" id="posDiscount" class="form-control form-control-sm text-end py-0" value="0" min="0">
                </div>
            </div>

            <div class="d-flex justify-content-between mb-2 small text-muted">
                <span>Tax / GST ({{ $defaultTaxPercent }}%):</span>
                <span class="fw-bold text-dark" id="posTax">₹0.00</span>
            </div>

            <hr class="my-2">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold fs-5">Grand Total:</span>
                <span class="fw-bold fs-4 text-primary" id="posGrandTotal">₹0.00</span>
            </div>

            <button class="btn btn-primary btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow" id="btnCheckout" disabled>
                <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                <span>Proceed to Pay (F2)</span>
            </button>
        </div>
    </div>

</div>

<!-- Payment Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cash-coin me-2"></i>Complete POS Sale & Billing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Primary Payment Tender <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group" id="paymentMethodButtonGroup">
                                <input type="radio" class="btn-check" name="payMethodRadio" id="payCash" value="cash" checked>
                                <label class="btn btn-outline-primary fw-semibold" for="payCash"><i class="bi bi-cash me-1"></i>Cash</label>

                                <input type="radio" class="btn-check" name="payMethodRadio" id="payUpi" value="upi">
                                <label class="btn btn-outline-primary fw-semibold" for="payUpi"><i class="bi bi-qr-code-scan me-1"></i>UPI</label>

                                <input type="radio" class="btn-check" name="payMethodRadio" id="payCard" value="card">
                                <label class="btn btn-outline-primary fw-semibold" for="payCard"><i class="bi bi-credit-card me-1"></i>Card</label>

                                <input type="radio" class="btn-check" name="payMethodRadio" id="paySplit" value="mixed">
                                <label class="btn btn-outline-primary fw-semibold" for="paySplit"><i class="bi bi-pie-chart me-1"></i>Split</label>

                                <input type="radio" class="btn-check" name="payMethodRadio" id="payCredit" value="credit">
                                <label class="btn btn-outline-primary fw-semibold" for="payCredit"><i class="bi bi-clock me-1"></i>Due</label>
                            </div>
                        </div>

                        <!-- Split Payment Details (Toggled when Split is picked) -->
                        <div id="splitPaymentDetails" style="display: none;" class="bg-light p-3 rounded mb-3 border">
                            <h6 class="fw-bold small mb-2 text-dark">Split Amounts</h6>
                            <div class="row g-2">
                                <div class="col-4">
                                    <label class="small text-muted">Cash</label>
                                    <input type="number" id="splitCash" class="form-control form-control-sm" value="0">
                                </div>
                                <div class="col-4">
                                    <label class="small text-muted">UPI</label>
                                    <input type="number" id="splitUpi" class="form-control form-control-sm" value="0">
                                </div>
                                <div class="col-4">
                                    <label class="small text-muted">Card</label>
                                    <input type="number" id="splitCard" class="form-control form-control-sm" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="standardPaidAmountGroup">
                            <label class="form-label fw-semibold small">Cash / Tender Amount Received (₹)</label>
                            <input type="number" step="0.01" id="modalPaidAmount" class="form-control form-control-lg fw-bold text-success" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Sale / Invoice Notes</label>
                            <input type="text" id="modalNotes" class="form-control" placeholder="Optional notes, customer request...">
                        </div>
                    </div>

                    <!-- Right Receipt Summary Box -->
                    <div class="col-md-5">
                        <div class="card bg-light border-0 p-3 h-100">
                            <h6 class="fw-bold border-bottom pb-2">Sale Breakdown</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Payable:</span>
                                <span class="fw-bold fs-5 text-primary" id="modalGrandTotal">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Paid:</span>
                                <span class="fw-semibold text-success" id="modalTotalPaid">₹0.00</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Change to Return:</span>
                                <span class="fw-bold fs-5 text-dark" id="modalChange">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-danger fw-semibold">Balance Due:</span>
                                <span class="fw-bold text-danger" id="modalDue">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-lg fw-bold px-4" id="btnConfirmSale">
                    <i class="bi bi-printer-fill me-1"></i> Complete & Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Quick Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" id="quickCustName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Mobile Number <span class="text-danger">*</span></label>
                    <input type="text" id="quickCustMobile" class="form-control" placeholder="10-digit mobile" required>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold small">Address / Notes</label>
                    <input type="text" id="quickCustAddress" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary fw-bold" id="btnSaveQuickCustomer">Save & Select</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const defaultTaxRate = {{ $defaultTaxPercent }};
    let cart = []; // holds { product_id, name, sku, type, price, cost, qty, imei_id, imei, serials: [] }
    let activeCategory = '';

    // 1. Fetch products via AJAX
    function fetchProducts(query = '', categoryId = '') {
        $.ajax({
            url: "{{ route('admin.pos.search') }}",
            type: "GET",
            data: { query: query, category_id: categoryId },
            success: function(res) {
                if (res.exact_imei_match) {
                    // Barcode scanned an exact IMEI! Automatically add it to cart!
                    addToCart(res.product, res.serial);
                    $('#posSearchInput').val('').focus();
                    return;
                }

                renderProductGrid(res.products);
            }
        });
    }

    // 2. Render Product Grid
    function renderProductGrid(products) {
        const grid = document.getElementById('productGrid');
        grid.innerHTML = '';

        if (!products || products.length === 0) {
            grid.innerHTML = `<div class="col-12 text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-1"></i>No products found</div>`;
            return;
        }

        products.forEach(p => {
            const card = document.createElement('div');
            card.className = 'product-card';
            card.onclick = () => addToCart(p);

            const typeBadge = p.type === 'mobile' 
                ? '<span class="badge bg-primary position-absolute top-0 end-0 m-2"><i class="bi bi-phone"></i> IMEI</span>'
                : `<span class="badge bg-secondary position-absolute top-0 end-0 m-2">${p.current_stock} left</span>`;

            card.innerHTML = `
                ${typeBadge}
                <div class="img-box">
                    ${p.image ? `<img src="/storage/${p.image}">` : `<i class="bi bi-phone fs-2 text-secondary"></i>`}
                </div>
                <div class="fw-bold small text-truncate" title="${p.name}">${p.name}</div>
                <div class="text-muted" style="font-size: 0.75rem;">${p.brand ? p.brand.name : ''}</div>
                <div class="mt-auto pt-2 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-success">₹${Number(p.selling_price).toLocaleString()}</span>
                    <button class="btn btn-sm btn-light border rounded-circle p-1"><i class="bi bi-plus fs-6"></i></button>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    // 3. Add Product to Cart
    function addToCart(product, specificSerial = null) {
        if (product.type === 'mobile') {
            const availableSerials = product.available_serials || [];
            if (availableSerials.length === 0 && !specificSerial) {
                Swal.fire('Out of Stock', `No available IMEIs for ${product.name}.`, 'warning');
                return;
            }

            const chosenSerial = specificSerial || availableSerials[0];

            // Check if already in cart
            const existing = cart.find(item => item.imei === chosenSerial.imei_1);
            if (existing) {
                Swal.fire('Already in Cart', `IMEI ${chosenSerial.imei_1} is already in the cart.`, 'info');
                return;
            }

            cart.push({
                product_id: product.id,
                name: product.name,
                sku: product.sku,
                type: 'mobile',
                price: parseFloat(product.selling_price),
                cost: parseFloat(chosenSerial.purchase_price || product.purchase_price),
                qty: 1,
                imei_id: chosenSerial.id,
                imei: chosenSerial.imei_1,
                available_serials: availableSerials
            });
        } else {
            // Accessory or spare part
            const existing = cart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.qty + 1 > product.current_stock) {
                    Swal.fire('Stock Limit', `Only ${product.current_stock} units available in stock.`, 'warning');
                    return;
                }
                existing.qty += 1;
            } else {
                if (product.current_stock < 1) {
                    Swal.fire('Out of Stock', `${product.name} is currently out of stock.`, 'warning');
                    return;
                }
                cart.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    type: 'accessory',
                    price: parseFloat(product.selling_price),
                    cost: parseFloat(product.purchase_price),
                    qty: 1,
                    imei_id: null,
                    imei: null
                });
            }
        }

        renderCart();
    }

    // 4. Render Cart
    function renderCart() {
        const container = document.getElementById('cartItemsContainer');
        const emptyMsg = document.getElementById('emptyCartMessage');

        if (cart.length === 0) {
            container.innerHTML = '';
            container.appendChild(emptyMsg);
            emptyMsg.style.display = 'block';
            document.getElementById('btnCheckout').disabled = true;
            updateTotals(0, 0, 0);
            return;
        }

        emptyMsg.style.display = 'none';
        container.innerHTML = '';

        cart.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'cart-item-row';

            let imeiSelector = '';
            if (item.type === 'mobile' && item.available_serials && item.available_serials.length > 1) {
                let opts = '';
                item.available_serials.forEach(s => {
                    opts += `<option value="${s.id}" data-imei="${s.imei_1}" ${s.id === item.imei_id ? 'selected' : ''}>IMEI: ${s.imei_1}</option>`;
                });
                imeiSelector = `<select class="form-select form-select-sm font-monospace mt-1 py-0" style="font-size: 0.75rem;" onchange="changeImei(${index}, this)">${opts}</select>`;
            } else if (item.imei) {
                imeiSelector = `<div class="font-monospace text-primary small">IMEI: ${item.imei}</div>`;
            }

            const qtyControls = item.type === 'mobile' 
                ? `<span class="badge bg-light text-dark border px-2 py-1">Qty: 1</span>`
                : `
                    <div class="input-group input-group-sm" style="width: 90px;">
                        <button class="btn btn-outline-secondary py-0" onclick="updateQty(${index}, -1)">-</button>
                        <input type="text" class="form-control text-center py-0 px-1" value="${item.qty}" readonly>
                        <button class="btn btn-outline-secondary py-0" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                `;

            row.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div class="flex-fill pe-2">
                        <div class="fw-bold small text-truncate" style="max-width: 260px;" title="${item.name}">${item.name}</div>
                        ${imeiSelector}
                    </div>
                    <button class="btn btn-sm text-danger p-0 border-0" onclick="removeFromCart(${index})">
                        <i class="bi bi-x-circle fs-6"></i>
                    </button>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div>${qtyControls}</div>
                    <div class="text-end">
                        <div class="fw-bold text-dark">₹${(item.price * item.qty).toLocaleString()}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">₹${item.price} each</small>
                    </div>
                </div>
            `;
            container.appendChild(row);
        });

        document.getElementById('btnCheckout').disabled = false;
        recalculatePosTotals();
    }

    function updateQty(index, delta) {
        if (cart[index].type === 'mobile') return;
        const newQty = cart[index].qty + delta;
        if (newQty <= 0) {
            removeFromCart(index);
        } else {
            cart[index].qty = newQty;
            renderCart();
        }
    }

    function changeImei(index, selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        cart[index].imei_id = selectEl.value;
        cart[index].imei = selectedOpt.dataset.imei;
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // 5. Calculate Subtotal, Tax, Discount, Grand Total
    function recalculatePosTotals() {
        let subtotal = 0;
        let count = 0;

        cart.forEach(item => {
            subtotal += item.price * item.qty;
            count += item.qty;
        });

        const discount = parseFloat($('#posDiscount').val()) || 0;
        const tax = 0; // standard inclusive or additional tax
        const grandTotal = Math.max(0, subtotal - discount + tax);

        updateTotals(subtotal, tax, grandTotal, count);
    }

    function updateTotals(sub, tax, grand, count = 0) {
        $('#cartItemsCount').text(count);
        $('#posSubtotal').text('₹' + sub.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#posTax').text('₹' + tax.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#posGrandTotal').text('₹' + grand.toLocaleString(undefined, { minimumFractionDigits: 2 }));
    }

    // 6. Checkout Modal & Handlers
    $('#btnCheckout').on('click', function() {
        if (cart.length === 0) return;

        const subtotal = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const discount = parseFloat($('#posDiscount').val()) || 0;
        const grandTotal = Math.max(0, subtotal - discount);

        $('#modalGrandTotal').text('₹' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#modalPaidAmount').val(grandTotal);
        updateModalCalculations(grandTotal, grandTotal);

        $('#checkoutModal').modal('show');
    });

    $('input[name="payMethodRadio"]').on('change', function() {
        const method = $(this).val();
        if (method === 'mixed') {
            $('#splitPaymentDetails').slideDown();
            $('#standardPaidAmountGroup').slideUp();
        } else if (method === 'credit') {
            $('#splitPaymentDetails').slideUp();
            $('#standardPaidAmountGroup').slideDown();
            $('#modalPaidAmount').val(0).trigger('input');
        } else {
            $('#splitPaymentDetails').slideUp();
            $('#standardPaidAmountGroup').slideDown();
            const grand = getModalGrandTotal();
            $('#modalPaidAmount').val(grand).trigger('input');
        }
    });

    $('#modalPaidAmount, #splitCash, #splitUpi, #splitCard').on('input', function() {
        const grand = getModalGrandTotal();
        let paid = 0;

        const method = $('input[name="payMethodRadio"]:checked').val();
        if (method === 'mixed') {
            paid = (parseFloat($('#splitCash').val()) || 0) + (parseFloat($('#splitUpi').val()) || 0) + (parseFloat($('#splitCard').val()) || 0);
        } else {
            paid = parseFloat($('#modalPaidAmount').val()) || 0;
        }

        updateModalCalculations(grand, paid);
    });

    function getModalGrandTotal() {
        const subtotal = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const discount = parseFloat($('#posDiscount').val()) || 0;
        return Math.max(0, subtotal - discount);
    }

    function updateModalCalculations(grand, paid) {
        const change = Math.max(0, paid - grand);
        const due = Math.max(0, grand - paid);

        $('#modalTotalPaid').text('₹' + paid.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#modalChange').text('₹' + change.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $('#modalDue').text('₹' + due.toLocaleString(undefined, { minimumFractionDigits: 2 }));
    }

    // 7. Submit Completed Sale
    $('#btnConfirmSale').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

        const grand = getModalGrandTotal();
        const discount = parseFloat($('#posDiscount').val()) || 0;
        const subtotal = cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
        const method = $('input[name="payMethodRadio"]:checked').val();

        let paid = 0;
        let payments = [];

        if (method === 'mixed') {
            const c = parseFloat($('#splitCash').val()) || 0;
            const u = parseFloat($('#splitUpi').val()) || 0;
            const k = parseFloat($('#splitCard').val()) || 0;
            paid = c + u + k;
            if (c > 0) payments.push({ method: 'cash', amount: c });
            if (u > 0) payments.push({ method: 'upi', amount: u });
            if (k > 0) payments.push({ method: 'card', amount: k });
        } else {
            paid = parseFloat($('#modalPaidAmount').val()) || 0;
            payments.push({ method: method, amount: Math.min(paid, grand) });
        }

        const customerId = $('#posCustomerSelect').val();

        const payload = {
            customer_id: customerId || null,
            subtotal: subtotal,
            discount_amount: discount,
            tax_amount: 0,
            grand_total: grand,
            paid_amount: paid,
            payment_method: method,
            notes: $('#modalNotes').val(),
            payments: payments,
            items: cart.map(item => ({
                product_id: item.product_id,
                product_serial_id: item.imei_id,
                imei: item.imei,
                quantity: item.qty,
                unit_price: item.price
            }))
        };

        $.ajax({
            url: "{{ route('admin.pos.store') }}",
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: JSON.stringify(payload),
            contentType: "application/json",
            success: function(res) {
                $('#checkoutModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Sale Completed!',
                    text: `Invoice #${res.invoice_no} generated successfully.`,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-printer"></i> View & Print Invoice',
                    cancelButtonText: 'New Sale'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = res.redirect_url;
                    } else {
                        cart = [];
                        renderCart();
                        fetchProducts();
                        $('#posSearchInput').focus();
                    }
                });
            },
            error: function(err) {
                btn.prop('disabled', false).html('<i class="bi bi-printer-fill me-1"></i> Complete & Print Invoice');
                const msg = err.responseJSON ? err.responseJSON.message : 'Error processing sale.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // 8. Quick Add Customer
    $('#btnSaveQuickCustomer').on('click', function() {
        const name = $('#quickCustName').val().trim();
        const mobile = $('#quickCustMobile').val().trim();
        const address = $('#quickCustAddress').val().trim();

        if (!name || !mobile) {
            Swal.fire('Required', 'Name and mobile number are required.', 'warning');
            return;
        }

        $.ajax({
            url: "{{ route('admin.customers.store') }}",
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { name: name, mobile: mobile, address: address },
            success: function(res) {
                $('#quickCustomerModal').modal('hide');
                const newOpt = new Option(`${res.customer.name} (${res.customer.mobile})`, res.customer.id, true, true);
                $('#posCustomerSelect').append(newOpt).trigger('change');
                Swal.fire('Success', 'Customer added and selected.', 'success');
            },
            error: function(err) {
                Swal.fire('Error', 'Could not create customer. Mobile might already exist.', 'error');
            }
        });
    });

    // 9. Keyboard Shortcuts & Event Listeners
    $(document).ready(function() {
        fetchProducts();

        $('#posSearchInput').on('input', function() {
            const q = $(this).val().trim();
            fetchProducts(q, activeCategory);
        });

        $('#clearSearchBtn').on('click', function() {
            $('#posSearchInput').val('');
            fetchProducts('', activeCategory);
        });

        $('.category-pill').on('click', function() {
            $('.category-pill').removeClass('active');
            $(this).addClass('active');
            activeCategory = $(this).data('cat');
            fetchProducts($('#posSearchInput').val().trim(), activeCategory);
        });

        $('#posDiscount').on('input', recalculatePosTotals);

        $('#clearCartBtn').on('click', function() {
            if (cart.length > 0) {
                if (confirm('Clear entire cart?')) {
                    cart = [];
                    renderCart();
                }
            }
        });

        // Global hotkeys
        $(document).on('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                $('#btnCheckout').click();
            } else if (e.key === 'F4') {
                e.preventDefault();
                $('#posSearchInput').focus();
            }
        });
    });
</script>
@endpush
