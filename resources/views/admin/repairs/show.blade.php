@extends('layouts.app')

@section('title', 'Repair #' . $repair->repair_number . ' - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-wrench me-2 text-primary"></i> Repair Details
            <small class="text-muted fs-6">#{{ $repair->repair_number }}</small>
        </h2>
        <div>
            <a href="{{ route('admin.repairs.edit', $repair->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.repairs.print', $repair->id) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print me-1"></i> Print
            </a>
            <a href="{{ route('admin.repairs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Repair Number</td>
                            <td>{{ $repair->repair_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Customer</td>
                            <td>{{ $repair->customer_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Customer Mobile</td>
                            <td>{{ $repair->customer_mobile }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Device Name</td>
                            <td>{{ $repair->device_name }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">IMEI</td>
                            <td>{{ $repair->imei ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Status</td>
                            <td>
                                <span class="badge" style="background-color: {{ $repair->repairStatus->color ?? '#6c757d' }}; color: #fff;">
                                    {{ $repair->repairStatus->name ?? 'Pending' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Estimated Cost</td>
                            <td class="fw-bold">Rs. {{ number_format($repair->estimated_cost, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Advance Paid</td>
                            <td class="text-success">Rs. {{ number_format($repair->advance_paid, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Remaining Amount</td>
                            <td class="text-danger">Rs. {{ number_format($repair->remaining_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Status</td>
                            <td>
                                @if($repair->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($repair->payment_status == 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Receive Date</td>
                            <td>{{ $repair->receive_date->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Delivery Date</td>
                            <td>{{ $repair->delivery_date ? $repair->delivery_date->format('d M, Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Accessories Received</td>
                            <td>{{ $repair->accessories_received ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Engineer Notes</td>
                            <td>{{ $repair->engineer_notes ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <h6 class="fw-bold text-muted">Issue Description</h6>
                <p>{{ $repair->issue }}</p>
            </div>

            @if($repair->images)
                <div class="mt-3">
                    <h6 class="fw-bold text-muted">Images</h6>
                    <div class="row">
                        @foreach($repair->images as $image)
                            <div class="col-md-3">
                                <img src="{{ Storage::url($image) }}" alt="Repair Image" class="img-fluid rounded">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($repair->documents)
                <div class="mt-3">
                    <h6 class="fw-bold text-muted">Documents</h6>
                    <ul>
                        @foreach($repair->documents as $document)
                            <li>
                                <a href="{{ Storage::url($document) }}" target="_blank">
                                    {{ basename($document) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 