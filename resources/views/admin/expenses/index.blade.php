@extends('layouts.admin')

@section('title', 'Expense Management')
@section('page_title', 'Business Expenses')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Expenses</h4>
            <small class="text-muted">Track shop overheads: rent, staff salary, tea/refreshments, electricity, and parts maintenance</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="bi bi-folder-plus me-1"></i> Expense Categories
            </button>
            <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-lg me-1"></i> Record Expense
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Total Filtered Expenses</div>
                <h4 class="fw-bold mb-0 mt-1 text-danger">₹{{ number_format($expenses->sum('amount'), 2) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">This Month's Overhead</div>
                <h4 class="fw-bold mb-0 mt-1 text-dark">
                    ₹{{ number_format(\App\Models\Expense::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount'), 2) }}
                </h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-muted small">Today's Expenses</div>
                <h4 class="fw-bold mb-0 mt-1 text-secondary">
                    ₹{{ number_format(\App\Models\Expense::whereDate('date', today())->sum('amount'), 2) }}
                </h4>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.expenses.index') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date" name="from_date" class="form-control form-control-sm" placeholder="From Date" value="{{ request('from_date') }}" onchange="this.form.submit()">
                </div>

                <div class="col-md-3">
                    <input type="date" name="to_date" class="form-control form-control-sm" placeholder="To Date" value="{{ request('to_date') }}" onchange="this.form.submit()">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-sm btn-light border">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Title / Description</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                        <th>Recorded By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->date->format('d M Y') }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $expense->category->name }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $expense->title }}</div>
                            @if($expense->description)
                                <small class="text-muted">{{ $expense->description }}</small>
                            @endif
                        </td>
                        <td class="fw-bold text-danger">₹{{ number_format($expense->amount, 2) }}</td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $expense->payment_method }}</span></td>
                        <td><small class="text-muted">{{ $expense->reference_no ?: '-' }}</small></td>
                        <td><small class="text-muted">{{ $expense->creator?->name ?? 'Admin' }}</small></td>
                        <td class="text-end">
                            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border py-0 px-2"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No expense records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal: Add Expense -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record New Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select name="expense_category_id" class="form-select" required>
                            <option value="">Select Category...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Expense Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Shop monthly rent or Staff tea & snacks" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control fw-bold" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="cash" selected>Cash</option>
                                <option value="upi">UPI / GPay / PhonePe</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Reference / Bill #</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="Optional ref">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Notes / Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Details..."></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Attach Bill / Receipt (Optional)</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,application/pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Manage Expense Categories -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.expenses.category.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Expense Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Add New Category</label>
                        <div class="input-group">
                            <input type="text" name="name" class="form-control" placeholder="Category name..." required>
                            <button type="submit" class="btn btn-primary fw-bold">Add</button>
                        </div>
                    </div>
                    <label class="form-label small fw-semibold text-muted">Existing Categories:</label>
                    <ul class="list-group list-group-flush border rounded">
                        @foreach($categories as $c)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                <span>{{ $c->name }}</span>
                                <span class="badge bg-light text-dark border">{{ $c->expenses_count }} Entries</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
