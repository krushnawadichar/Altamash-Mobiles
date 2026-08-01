@extends('layouts.app')

@section('title', 'Expenses - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-money-bill-wave me-2 text-primary"></i> Expenses
        </h2>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Expense
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $expense->title }}</td>
                                <td>{{ $expense->expenseCategory->name ?? 'N/A' }}</td>
                                <td>Rs. {{ number_format($expense->amount, 0) }}</td>
                                <td>{{ $expense->expense_date->format('d M, Y') }}</td>
                                <td>{{ ucfirst($expense->payment_method) }}</td>
                                <td>
                                    @if($expense->status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($expense->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.expenses.edit', $expense->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.expenses.destroy', $expense->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No expenses found.</p>
                                    <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Expense
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection