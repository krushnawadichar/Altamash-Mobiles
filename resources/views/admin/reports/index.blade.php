@extends('layouts.app')

@section('title', 'Reports - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-chart-line me-2 text-primary"></i> Reports
        </h2>
    </div>

    <div class="row g-3">
        <!-- Report Cards -->
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 mb-3 d-inline-block">
                        <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Sales Report</h5>
                    <p class="text-muted small">View and export sales data</p>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-arrow-right me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 mb-3 d-inline-block">
                        <i class="fas fa-shopping-bag fa-2x text-success"></i>
                    </div>
                    <h5 class="fw-bold">Purchase Report</h5>
                    <p class="text-muted small">View and export purchase data</p>
                    <a href="{{ route('admin.reports.purchases') }}" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-arrow-right me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-info bg-opacity-10 p-3 mb-3 d-inline-block">
                        <i class="fas fa-chart-pie fa-2x text-info"></i>
                    </div>
                    <h5 class="fw-bold">Profit Report</h5>
                    <p class="text-muted small">View and export profit data</p>
                    <a href="{{ route('admin.reports.profit') }}" class="btn btn-info btn-sm w-100 text-white">
                        <i class="fas fa-arrow-right me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 mb-3 d-inline-block">
                        <i class="fas fa-money-bill-wave fa-2x text-danger"></i>
                    </div>
                    <h5 class="fw-bold">Expense Report</h5>
                    <p class="text-muted small">View and export expense data</p>
                    <a href="{{ route('admin.reports.expenses') }}" class="btn btn-danger btn-sm w-100">
                        <i class="fas fa-arrow-right me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3 mb-3 d-inline-block">
                        <i class="fas fa-wrench fa-2x text-warning"></i>
                    </div>
                    <h5 class="fw-bold">Repair Report</h5>
                    <p class="text-muted small">View and export repair data</p>
                    <a href="{{ route('admin.reports.repairs') }}" class="btn btn-warning btn-sm w-100">
                        <i class="fas fa-arrow-right me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-secondary bg-opacity-10 p-3 mb-3 d-inline-block">
                        <i class="fas fa-warehouse fa-2x text-secondary"></i>
                    </div>
                    <h5 class="fw-bold">Inventory Report</h5>
                    <p class="text-muted small">View and export inventory data</p>
                    <a href="{{ route('admin.reports.inventory') }}" class="btn btn-secondary btn-sm w-100">
                        <i class="fas fa-arrow-right me-1"></i> View Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection