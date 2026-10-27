@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Products</h2>
            <p class="text-muted mb-0">Manage products, scan barcodes, and monitor stock inventory</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary px-3">
                <i class="fa-solid fa-plus me-1"></i> Add Product
            </a>
        </div>
    </div>

    <!-- AJAX Filter & Barcode Scanner Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4">
            <form id="filterForm" onsubmit="return false;">
                <div class="row g-3 align-items-center">
                    
                    <!-- Search & Barcode Scan Input -->
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label fw-bold small text-muted mb-1">
                            <i class="fa-solid fa-barcode text-primary me-1"></i> Scan Barcode or Search
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-barcode text-secondary"></i>
                            </span>
                            <input type="text" 
                                   id="ajaxSearch" 
                                   name="search" 
                                   class="form-control border-start-0" 
                                   placeholder="Scan barcode with gun scanner or type name, SKU..." 
                                   value="{{ request('search') }}"
                                   autofocus
                                   autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="btnClearSearch" title="Clear Search">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-bold small text-muted mb-1">Category</label>
                        <select id="ajaxCategory" name="category_id" class="form-select">
                            <option value="">-- All Categories --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold small text-muted mb-1">Brand</label>
                        <select id="ajaxBrand" name="brand_id" class="form-select">
                            <option value="">-- All Brands --</option>
                            @if(isset($brands))
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label fw-bold small text-muted mb-1">Stock Level</label>
                        <select id="ajaxStock" name="stock_status" class="form-select">
                            <option value="">-- All Stock --</option>
                            <option value="in_stock">In Stock (> 0)</option>
                            <option value="low_stock">Low Stock Alert</option>
                            <option value="out_of_stock">Out of Stock (0)</option>
                        </select>
                    </div>

                    <!-- Reset / Action -->
                    <div class="col-lg-1 col-md-12 text-md-end mt-lg-4">
                        <button type="button" id="btnResetFilters" class="btn btn-outline-secondary w-100" title="Reset all filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>

                </div>
            </form>

            <!-- Status Indicator Bar -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top small text-muted">
                <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle me-2">
                        <i class="fa-solid fa-circle-dot me-1"></i>Gun Scanner Active
                    </span>
                    <span>Pull the gun scanner trigger anytime to filter instantly.</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div id="filterLoading" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span id="resultsCountBadge" class="fw-semibold text-dark">
                        Showing {{ $products->count() }} product(s)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 70px;">ID</th>
                            <th style="width: 70px;">Image</th>
                            <th>Product Details</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @include('admin.products._table_rows', ['products' => $products])
                    </tbody>
                </table>
            </div>
        <!-- Card Footer with Pagination -->
        <div class="card-footer bg-white py-3 border-top">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="text-muted small" id="paginationInfo">
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products (Latest First)
                </div>
                <div id="paginationContainer">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let filterTimer = null;

    // Web Audio Synthesizer Beep on Barcode Scan
    function playScanBeep() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1750, ctx.currentTime);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.12);
        } catch(e) {}
    }

    // Perform AJAX Filtering with Pagination
    function fetchFilteredProducts(page = 1) {
        const searchVal = $('#ajaxSearch').val().trim();
        const categoryVal = $('#ajaxCategory').val();
        const brandVal = $('#ajaxBrand').val();
        const stockVal = $('#ajaxStock').val();

        $('#filterLoading').removeClass('d-none');

        $.ajax({
            url: "{{ route('admin.products.index') }}",
            type: "GET",
            data: {
                page: page,
                search: searchVal,
                category_id: categoryVal,
                brand_id: brandVal,
                stock_status: stockVal
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(res) {
                $('#productsTableBody').html(res.html);
                $('#paginationContainer').html(res.pagination);
                $('#paginationInfo').text(`Showing ${res.from} to ${res.to} of ${res.count} products (Latest First)`);
                $('#resultsCountBadge').text(`Showing ${res.count} product(s)`);
                $('#filterLoading').addClass('d-none');

                // If searched by barcode and found exact 1 product, highlight row
                if (searchVal.length > 5 && res.count === 1) {
                    playScanBeep();
                    $('#productsTableBody tr:first').addClass('table-success');
                    setTimeout(() => {
                        $('#productsTableBody tr:first').removeClass('table-success');
                    }, 1200);
                }
            },
            error: function(err) {
                console.error('Filter error', err);
                $('#filterLoading').addClass('d-none');
            }
        });
    }

    $(document).ready(function() {
        const $search = $('#ajaxSearch');

        // Debounced typing search (resets to page 1)
        $search.on('input', function() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(() => fetchFilteredProducts(1), 300);
        });

        // Barcode Gun Scanner Enter Key:
        // Barcode gun scanners emit characters and finish with an Enter key (13).
        // Catch Enter to prevent form submission and trigger immediate AJAX filtering!
        $search.on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                clearTimeout(filterTimer);
                fetchFilteredProducts(1);
            }
        });

        // Dropdowns change trigger instant AJAX filter (page 1)
        $('#ajaxCategory, #ajaxBrand, #ajaxStock').on('change', function() {
            fetchFilteredProducts(1);
        });

        // Clear search input button
        $('#btnClearSearch').on('click', function() {
            $search.val('').focus();
            fetchFilteredProducts(1);
        });

        // Reset all filters button
        $('#btnResetFilters').on('click', function() {
            $search.val('');
            $('#ajaxCategory').val('');
            $('#ajaxBrand').val('');
            $('#ajaxStock').val('');
            $search.focus();
            fetchFilteredProducts(1);
        });

        // Intercept AJAX Pagination Links
        $(document).on('click', '#paginationContainer a', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            if (!href) return;
            const urlParams = new URLSearchParams(href.split('?')[1]);
            const page = urlParams.get('page') || 1;
            fetchFilteredProducts(page);
            // Smoothly scroll to top of table
            $('html, body').animate({
                scrollTop: $('#productsTable').offset().top - 90
            }, 250);
        });
    });
</script>
@endpush
@endsection
