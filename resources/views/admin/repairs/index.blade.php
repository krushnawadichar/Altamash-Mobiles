@extends('layouts.app')

@section('title', 'Repairs - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-wrench me-2 text-primary"></i> Repairs
        </h2>
        <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add Repair
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Repair #</th>
                            <th>Customer</th>
                            <th>Device</th>
                            <th>Status</th>
                            <th>Estimated Cost</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($repairs as $repair)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $repair->repair_number }}</td>
                                <td>{{ $repair->customer_name }}</td>
                                <td>{{ $repair->device_name }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $repair->repairStatus->color ?? '#6c757d' }}">
                                        {{ $repair->repairStatus->name ?? 'Pending' }}
                                    </span>
                                </td>
                                <td>Rs. {{ number_format($repair->estimated_cost, 0) }}</td>
                                <td>Rs. {{ number_format($repair->advance_paid, 0) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.repairs.show', $repair->id) }}" 
                                           class="btn btn-info text-white">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.repairs.edit', $repair->id) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.repairs.print', $repair->id) }}" 
                                           class="btn btn-secondary" target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <form action="{{ route('admin.repairs.destroy', $repair->id) }}" 
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
                                    <p class="text-muted">No repairs found.</p>
                                    <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> Add Repair
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