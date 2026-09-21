@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Business Overview & Dashboard')

@section('content')
<div class="container-fluid px-0">

    <!-- Top Row 1: Core Financial & Sales Stats -->
    <div class="row g-3 mb-4">
        <!-- Today's Sales -->
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: #fff;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Today's Sales</span>
                            <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($todaySales, 2) }}</h3>
                            <small class="text-white-50" style="font-size: 0.75rem;">Today's Invoiced Revenue</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-cart-check-fill fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Profit -->
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #fff;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Today's Profit</span>
                            <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($todayProfit, 2) }}</h3>
                            <small class="text-white-50" style="font-size: 0.75rem;">Sales - COGS - Expenses</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Collection -->
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Today's Collection</span>
                            <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($todayCollection, 2) }}</h3>
                            <small class="text-white-50" style="font-size: 0.75rem;">Cash/UPI/Card received</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Purchases -->
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #fff;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-white-50 text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Today's Purchase</span>
                            <h3 class="fw-bold mb-0 mt-1">₹{{ number_format($todayPurchases, 2) }}</h3>
                            <small class="text-white-50" style="font-size: 0.75rem;">Stock Intake Value</small>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.2);">
                            <i class="bi bi-truck fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Inventory & Outstanding Dues Metrics -->
    <div class="row g-3 mb-4">
        <!-- Total Products -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-primary-subtle text-primary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-boxes fs-5"></i>
                    </div>
                    <div class="text-muted small fw-semibold">Total Products</div>
                    <h4 class="fw-bold mb-0 mt-1">{{ $totalProducts }}</h4>
                </div>
            </div>
        </div>

        <!-- Total Mobile Phones -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-info-subtle text-info mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-phone fs-5"></i>
                    </div>
                    <div class="text-muted small fw-semibold">Mobile Models</div>
                    <h4 class="fw-bold mb-0 mt-1">{{ $totalMobiles }}</h4>
                </div>
            </div>
        </div>

        <!-- Total Accessories -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-secondary-subtle text-secondary mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-headphones fs-5"></i>
                    </div>
                    <div class="text-muted small fw-semibold">Accessories</div>
                    <h4 class="fw-bold mb-0 mt-1">{{ $totalAccessories }}</h4>
                </div>
            </div>
        </div>

        <!-- Total Stock Valuation -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-success-subtle text-success mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-currency-rupee fs-5"></i>
                    </div>
                    <div class="text-muted small fw-semibold">Stock Value</div>
                    <h5 class="fw-bold mb-0 mt-1 text-success">₹{{ number_format($totalStockValue, 0) }}</h5>
                </div>
            </div>
        </div>

        <!-- Pending Customer Payments -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-danger-subtle text-danger mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-person-exclamation fs-5"></i>
                    </div>
                    <div class="text-muted small fw-semibold">Customer Dues</div>
                    <h5 class="fw-bold mb-0 mt-1 text-danger">₹{{ number_format($pendingCustomerPayments, 0) }}</h5>
                </div>
            </div>
        </div>

        <!-- Pending Supplier Payments -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-warning-subtle text-warning mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                    <div class="text-muted small fw-semibold">Supplier Dues</div>
                    <h5 class="fw-bold mb-0 mt-1 text-warning">₹{{ number_format($pendingSupplierPayments, 0) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Repair Module Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Repairs -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Repair Jobs</div>
                        <h4 class="fw-bold mb-0 mt-1">{{ $totalRepairs }}</h4>
                    </div>
                    <div class="p-2 bg-primary-subtle text-primary rounded-3"><i class="bi bi-tools fs-4"></i></div>
                </div>
            </div>
        </div>

        <!-- Pending Repairs -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Repairs in Progress</div>
                        <h4 class="fw-bold mb-0 mt-1 text-primary">{{ $pendingRepairs }}</h4>
                    </div>
                    <div class="p-2 bg-info-subtle text-info rounded-3"><i class="bi bi-hourglass-split fs-4"></i></div>
                </div>
            </div>
        </div>

        <!-- Repairs Ready for Delivery -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Ready for Delivery</div>
                        <h4 class="fw-bold mb-0 mt-1 text-success">{{ $readyRepairs }}</h4>
                    </div>
                    <div class="p-2 bg-success-subtle text-success rounded-3"><i class="bi bi-box2-check-fill fs-4"></i></div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Low Stock Items</div>
                        <h4 class="fw-bold mb-0 mt-1 text-danger">{{ $lowStockCount }}</h4>
                    </div>
                    <div class="p-2 bg-danger-subtle text-danger rounded-3"><i class="bi bi-exclamation-octagon-fill fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Interactive Charts -->
    <div class="row g-3 mb-4">
        <!-- Sales & Purchase Comparison -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Sales vs. Purchase Trends (Last 7 Days)</span>
                    <span class="badge bg-light text-muted border">Dynamic Overview</span>
                </div>
                <div class="card-body">
                    <canvas id="salesPurchaseChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Profit Trend -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-pie-chart me-2 text-success"></i>Daily Profit Trend</span>
                    <span class="badge bg-success-subtle text-success">Net</span>
                </div>
                <div class="card-body">
                    <canvas id="profitChart" height="240"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 5: Recent Transactions & Tables -->
    <div class="row g-3 mb-4">
        <!-- Recent Sales -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-receipt me-2 text-primary"></i>Recent Sales Invoices</span>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-light border py-0 px-2 small">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="fw-bold text-decoration-none">
                                        {{ $sale->invoice_no }}
                                    </a>
                                </td>
                                <td>{{ $sale->customer_name ?: 'Walk-in' }}</td>
                                <td class="fw-bold">₹{{ number_format($sale->grand_total, 2) }}</td>
                                <td><span class="badge {{ $sale->payment_badge_class }}">{{ ucfirst($sale->payment_status) }}</span></td>
                                <td class="text-muted small">{{ $sale->sale_date->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No recent sales found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</span>
                    <a href="{{ route('admin.inventory.index', ['status' => 'low_stock']) }}" class="btn btn-sm btn-light border py-0 px-2 small">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Min Required</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $prod)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $prod->name }}</div>
                                    <small class="text-muted">SKU: {{ $prod->sku }}</small>
                                </td>
                                <td>{{ $prod->category?->name ?? 'General' }}</td>
                                <td><span class="badge bg-danger">{{ $prod->current_stock }} left</span></td>
                                <td>{{ $prod->min_stock }}</td>
                                <td>
                                    <a href="{{ route('admin.purchases.create') }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Create Purchase Order">
                                        <i class="bi bi-cart-plus"></i> Reorder
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">All items are sufficiently stocked!</td>
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

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dates = @json($chartDates);
        const salesData = @json($salesChartData);
        const purchaseData = @json($purchaseChartData);
        const profitData = @json($profitChartData);

        // Sales & Purchase Trend Chart
        const ctxSales = document.getElementById('salesPurchaseChart').getContext('2d');
        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Sales (₹)',
                        data: salesData,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Purchases (₹)',
                        data: purchaseData,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                return c.dataset.label + ': ₹' + Number(c.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return '₹' + v.toLocaleString(); }
                        }
                    }
                }
            }
        });

        // Daily Profit Trend Chart
        const ctxProfit = document.getElementById('profitChart').getContext('2d');
        new Chart(ctxProfit, {
            type: 'bar',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Net Profit (₹)',
                    data: profitData,
                    backgroundColor: profitData.map(val => val >= 0 ? '#10b981' : '#ef4444'),
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                return 'Profit: ₹' + Number(c.raw).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) { return '₹' + v.toLocaleString(); }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
