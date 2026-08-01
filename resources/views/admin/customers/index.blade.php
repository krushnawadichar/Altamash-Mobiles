@extends('layouts.app')

@section('title', 'Customers - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-user-friends me-2 text-primary"></i> Customers
        </h2>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Customer
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Purchases</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ number_format($customer->total_purchases) }}</td>
                                <td>Rs. {{ number_format($customer->current_balance, 0) }}</td>
                                <td>
                                    @if($customer->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.customers.show', $customer->id) }}" 
                                           class="btn btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" 
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
                                    <p class="text-muted">No customers found.</p>
                                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Customer
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