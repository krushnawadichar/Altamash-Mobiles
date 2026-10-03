@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Expenses</h2>
        <p class="text-muted">Manage daily shop expenses</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i> Add Expense</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title text-white-50">Total Expenses (This Month)</h6>
                <h3 class="mb-0">₹{{ number_format($totalThisMonth, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Added By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_date->format('d M, Y') }}</td>
                        <td class="fw-bold">{{ $expense->title }}</td>
                        <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                        <td class="fw-bold text-danger">₹{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->creator->name ?? 'System' }}</td>
                        <td>
                            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@endsection


