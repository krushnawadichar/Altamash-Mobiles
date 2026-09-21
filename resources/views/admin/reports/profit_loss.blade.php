@extends('layouts.admin')

@section('title', 'Profit & Loss Statement')
@section('page_title', 'Financial Statement (P&L)')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Profit & Loss Statement</h4>
            <small class="text-muted">Consolidated revenue, cost of goods, servicing revenue, expenses, and net profit</small>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark btn-sm no-print">
            <i class="bi bi-printer me-1"></i> Print P&L Statement
        </button>
    </div>

    <!-- Date Filters -->
    <div class="card border-0 shadow-sm mb-3 no-print">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.reports.profit-loss') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">From Date</label>
                    <input type="date" name="from_date" class="form-control form-control-sm" value="{{ $fromDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">To Date</label>
                    <input type="date" name="to_date" class="form-control form-control-sm" value="{{ $toDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Calculate Statement</button>
                    <a href="{{ route('admin.reports.profit-loss') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- P&L Statement Sheet -->
    <div class="card border-0 shadow-sm mb-4" style="max-width: 850px; margin: 0 auto;">
        <div class="card-body p-4">
            <div class="text-center border-bottom pb-3 mb-4">
                <h4 class="fw-bold text-primary mb-1">{{ \App\Models\Setting::get('shop_name', 'MobileCare') }}</h4>
                <div class="fw-semibold text-uppercase text-dark">Statement of Profit and Loss</div>
                <div class="text-muted small">For period: <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</strong></div>
            </div>

            <!-- Revenue Section -->
            <h6 class="fw-bold text-uppercase small text-muted border-bottom pb-1 mb-2">1. Operating Revenue</h6>
            <table class="table datatable table-borderless table-sm align-middle mb-3" style="font-size: 0.92rem;">
                <tbody>
                    <tr>
                        <td class="ps-3">Sales Revenue (Smartphones & Accessories)</td>
                        <td class="text-end fw-semibold">₹{{ number_format($salesRevenue, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3">Repair Service Fees & Labor</td>
                        <td class="text-end fw-semibold">₹{{ number_format($repairRevenue, 2) }}</td>
                    </tr>
                    <tr class="border-top fw-bold bg-light">
                        <td>TOTAL GROSS REVENUE</td>
                        <td class="text-end text-primary fs-6">₹{{ number_format($salesRevenue + $repairRevenue, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Cost of Goods Sold Section -->
            <h6 class="fw-bold text-uppercase small text-muted border-bottom pb-1 mb-2">2. Cost of Sales / Procurement</h6>
            <table class="table datatable table-borderless table-sm align-middle mb-3" style="font-size: 0.92rem;">
                <tbody>
                    <tr>
                        <td class="ps-3">Cost of Sold Products (Purchase Cost)</td>
                        <td class="text-end text-danger fw-semibold">₹{{ number_format($salesCost, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="ps-3">Cost of Spare Parts Used in Repairs</td>
                        <td class="text-end text-danger fw-semibold">₹{{ number_format($repairPartsCost, 2) }}</td>
                    </tr>
                    <tr class="border-top fw-bold bg-light">
                        <td>TOTAL COST OF GOODS SOLD (COGS)</td>
                        <td class="text-end text-danger fs-6">₹{{ number_format($totalCogs, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Gross Profit -->
            <div class="p-3 bg-primary-subtle rounded-3 d-flex justify-content-between align-items-center mb-4 border border-primary-subtle">
                <span class="fw-bold fs-6 text-primary">GROSS PROFIT (Revenue - COGS)</span>
                <span class="fw-bold fs-5 text-primary">₹{{ number_format($grossProfit, 2) }}</span>
            </div>

            <!-- Overhead Expenses Section -->
            <h6 class="fw-bold text-uppercase small text-muted border-bottom pb-1 mb-2">3. Operating Expenses</h6>
            <table class="table datatable table-borderless table-sm align-middle mb-3" style="font-size: 0.92rem;">
                <tbody>
                    @forelse($expensesByCategory as $catName => $amount)
                    <tr>
                        <td class="ps-3">{{ $catName }}</td>
                        <td class="text-end text-secondary fw-semibold">₹{{ number_format($amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="ps-3 text-muted">No overhead expenses recorded in this period</td>
                        <td class="text-end text-muted">₹0.00</td>
                    </tr>
                    @endforelse
                    <tr class="border-top fw-bold bg-light">
                        <td>TOTAL OPERATING EXPENSES</td>
                        <td class="text-end text-danger fs-6">₹{{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Net Profit / Loss -->
            <div class="p-4 rounded-3 d-flex justify-content-between align-items-center border {{ $netProfit >= 0 ? 'bg-success-subtle border-success-subtle' : 'bg-danger-subtle border-danger-subtle' }}">
                <div>
                    <h5 class="fw-bold mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $netProfit >= 0 ? 'NET PROFIT' : 'NET LOSS' }}
                    </h5>
                    <small class="text-muted">Gross Profit minus Operating Overheads</small>
                </div>
                <div class="fw-bold fs-3 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    ₹{{ number_format($netProfit, 2) }}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
