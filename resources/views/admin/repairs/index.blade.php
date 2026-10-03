@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Repairs & Services</h2>
        <p class="text-muted">Manage customer devices brought in for repair</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.repairs.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i> Add Repair Job</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Received</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th>Est. Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($repairs as $repair)
                    <tr>
                        <td class="fw-bold text-primary">{{ $repair->repair_code }}</td>
                        <td>{{ $repair->received_date->format('d M, Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $repair->customer_name }}</div>
                            <small class="text-muted">{{ $repair->customer_phone }}</small>
                        </td>
                        <td>{{ Str::limit($repair->device_name, 20) }}</td>
                        <td>₹{{ number_format($repair->estimated_cost, 2) }}</td>
                        <td>
                            @if($repair->status == 'Received')
                                <span class="badge bg-secondary">Received</span>
                            @elseif($repair->status == 'In Progress')
                                <span class="badge bg-primary">In Progress</span>
                            @elseif($repair->status == 'Waiting for Parts')
                                <span class="badge bg-warning text-dark">Waiting for Parts</span>
                            @elseif($repair->status == 'Ready')
                                <span class="badge bg-success">Ready</span>
                            @elseif($repair->status == 'Delivered')
                                <span class="badge bg-info text-dark">Delivered</span>
                            @else
                                <span class="badge bg-danger">{{ $repair->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.repairs.edit', $repair) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-edit"></i></a>
                            <form action="{{ route('admin.repairs.destroy', $repair) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this repair record?');">
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


