@extends('layouts.app')

@section('title', 'Dashboard - Altamash Mobiles')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .stat-card .card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    .stat-card .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.8;
        margin-bottom: 2px;
        color:black;
        font-weight: bold;
    }
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .quick-action-btn {
        border-radius: 20px;
        padding: 0.5rem 1.2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
        </h2>
        <div>
            <button class="btn btn-outline-secondary btn-sm" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="row g-2 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.sales.create') }}" class="btn btn-success quick-action-btn">
                    <i class="fas fa-plus-circle me-1"></i> New Sale
                </a>
                <a href="{{ route('admin.purchases.create') }}" class="btn btn-info text-white quick-action-btn">
                    <i class="fas fa-plus-circle me-1"></i> New Purchase
                </a>
                <a href="{{ route('admin.repairs.create') }}" class="btn btn-warning text-dark quick-action-btn">
                    <i class="fas fa-plus-circle me-1"></i> New Repair
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary quick-action-btn">
                    <i class="fas fa-plus-circle me-1"></i> Add Product
                </a>
                <a href="{{ route('admin.expenses.create') }}" class="btn btn-danger quick-action-btn">
                    <i class="fas fa-plus-circle me-1"></i> Add Expense
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="card stat-card border-0 bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Today's Sales</p>
                            <h3 class="stat-value">Rs. {{ number_format($stats['today_sales'] ?? 0, 0) }}</h3>
                        </div>
                        <div class="card-icon bg-white bg-opacity-20">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="card stat-card border-0 bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Today's Profit</p>
                            <h3 class="stat-value">Rs. {{ number_format($stats['today_profit'] ?? 0, 0) }}</h3>
                        </div>
                        <div class="card-icon bg-white bg-opacity-20">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="card stat-card border-0 bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Today's Purchases</p>
                            <h3 class="stat-value">Rs. {{ number_format($stats['today_purchases'] ?? 0, 0) }}</h3>
                        </div>
                        <div class="card-icon bg-white bg-opacity-20">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
            <div class="card stat-card border-0 bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Today's Expenses</p>
                            <h3 class="stat-value">Rs. {{ number_format($stats['today_expenses'] ?? 0, 0) }}</h3>
                        </div>
                        <div class="card-icon bg-white bg-opacity-20">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label text-muted">Total Products</p>
                            <h4 class="stat-value">{{ number_format($stats['total_products'] ?? 0) }}</h4>
                        </div>
                        <div class="card-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label text-muted">Low Stock</p>
                            <h4 class="stat-value text-warning">{{ number_format($stats['low_stock'] ?? 0) }}</h4>
                        </div>
                        <div class="card-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label text-muted">Out of Stock</p>
                            <h4 class="stat-value text-danger">{{ number_format($stats['out_of_stock'] ?? 0) }}</h4>
                        </div>
                        <div class="card-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label text-muted">Customers</p>
                            <h4 class="stat-value">{{ number_format($stats['total_customers'] ?? 0) }}</h4>
                        </div>
                        <div class="card-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label text-muted">Suppliers</p>
                            <h4 class="stat-value">{{ number_format($stats['total_suppliers'] ?? 0) }}</h4>
                        </div>
                        <div class="card-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label text-muted">Pending Repairs</p>
                            <h4 class="stat-value text-warning">{{ number_format($stats['pending_repairs'] ?? 0) }}</h4>
                        </div>
                        <div class="card-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-wrench"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i> Monthly Sales
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-line text-success me-2"></i> Monthly Purchases
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="purchaseChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-shopping-cart text-success me-2"></i> Recent Sales
                    </h5>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentSales ?? [] as $sale)
                            <a href="{{ route('admin.sales.show', $sale->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">{{ $sale->invoice_number }}</span>
                                        <small class="d-block text-muted">{{ $sale->customer->name ?? 'Walk-in' }}</small>
                                    </div>
                                    <span class="badge bg-success">Rs. {{ number_format($sale->total_amount, 0) }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                <span>No recent sales</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-wrench text-warning me-2"></i> Recent Repairs
                    </h5>
                    <a href="{{ route('admin.repairs.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentRepairs ?? [] as $repair)
                            <a href="{{ route('admin.repairs.show', $repair->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">{{ $repair->repair_number }}</span>
                                        <small class="d-block text-muted">{{ $repair->device_name }}</small>
                                    </div>
                                    <span class="badge" style="background-color: {{ $repair->repairStatus->color ?? '#6c757d' }}; color: #fff;">
                                        {{ $repair->repairStatus->name ?? 'Pending' }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                <span>No recent repairs</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-shopping-bag text-info me-2"></i> Recent Purchases
                    </h5>
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentPurchases ?? [] as $purchase)
                            <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">{{ $purchase->invoice_number }}</span>
                                        <small class="d-block text-muted">{{ $purchase->supplier->name ?? 'N/A' }}</small>
                                    </div>
                                    <span class="badge bg-info">Rs. {{ number_format($purchase->total_amount, 0) }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                <span>No recent purchases</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-star text-warning me-2"></i> Top Selling Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Total Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts ?? [] as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->total_sold ?? 0) }}</td>
                                        <td>Rs. {{ number_format($product->total_revenue ?? 0, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                            No data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart Data
    const salesLabels = @json($salesChart['labels'] ?? []);
    const salesData = @json($salesChart['data'] ?? []);
    
    // Purchase Chart Data
    const purchaseLabels = @json($purchaseChart['labels'] ?? []);
    const purchaseData = @json($purchaseChart['data'] ?? []);

    // Sales Chart
    if (document.getElementById('salesChart')) {
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: salesLabels.length ? salesLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales Amount (Rs.)',
                    data: salesData.length ? salesData : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(13, 110, 253, 0.5)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Purchase Chart
    if (document.getElementById('purchaseChart')) {
        const purchaseCtx = document.getElementById('purchaseChart').getContext('2d');
        new Chart(purchaseCtx, {
            type: 'line',
            data: {
                labels: purchaseLabels.length ? purchaseLabels : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Purchase Amount (Rs.)',
                    data: purchaseData.length ? purchaseData : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush