@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Customers</h2>
        <p class="text-muted">Manage store customers</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i> Add Customer</a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>City</th>
                        <th>Total Purchases</th>
                        <th>Pending Due</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td>
                            {{ $customer->phone ?? 'N/A' }}
                            @if($customer->phone)
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                                    if (strlen($cleanPhone) === 10) $cleanPhone = '91' . $cleanPhone;
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none ms-1" title="Chat on WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            @endif
                        </td>
                        <td>{{ $customer->email ?? 'N/A' }}</td>
                        <td>{{ $customer->city ?? 'N/A' }}</td>
                        <td class="text-success fw-bold">₹{{ number_format($customer->total_purchases, 2) }}</td>
                        <td>
                            @php $due = $customer->total_due; @endphp
                            @if($due > 0)
                                <span class="badge bg-danger fs-6">₹{{ number_format($due, 2) }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success border">₹0.00</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-info" title="View Invoices & Orders">
                                <i class="fa-solid fa-file-invoice me-1"></i> Invoices
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary" title="Edit Customer">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete Customer"><i class="fa-solid fa-trash"></i></button>
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


