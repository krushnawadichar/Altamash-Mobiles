@extends('layouts.app')

@section('title', 'Purchase #' . $purchase->invoice_number . ' - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-shopping-bag me-2 text-primary"></i> Purchase Details
            <small class="text-muted fs-6">#{{ $purchase->invoice_number }}</small>
        </h2>
        <div>
            <a href="{{ route('admin.purchases.print', $purchase->id) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print me-1"></i> Print
            </a>
            <a href="{{ route('admin.purchases.pdf', $purchase->id) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">
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
                            <td class="fw-bold text-muted" style="width: 150px;">Invoice Number</td>
                            <td>{{ $purchase->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Supplier</td>
                            <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Purchase Date</td>
                            <td>{{ $purchase->purchase_date->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Status</td>
                            <td>
                                @if($purchase->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Payment Status</td>
                            <td>
                                @if($purchase->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($purchase->payment_status == 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Amount</td>
                            <td class="fw-bold">Rs. {{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Paid Amount</td>
                            <td class="text-success">Rs. {{ number_format($purchase->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Due Amount</td>
                            <td class="text-danger">Rs. {{ number_format($purchase->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($purchase->notes)
                <div class="mt-2">
                    <h6 class="fw-bold text-muted">Notes</h6>
                    <p>{{ $purchase->notes }}</p>
                </div>
            @endif

            <div class="mt-4">
                <h5 class="fw-bold">Items</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->purchasable->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ class_basename($detail->purchasable_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>Rs. {{ number_format($detail->purchase_price, 2) }}</td>
                                    <td>Rs. {{ number_format($detail->selling_price, 2) }}</td>
                                    <td class="fw-bold">Rs. {{ number_format($detail->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Subtotal:</td>
                                <td class="fw-bold">Rs. {{ number_format($purchase->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Discount:</td>
                                <td class="fw-bold text-success">- Rs. {{ number_format($purchase->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">GST (18%):</td>
                                <td class="fw-bold">Rs. {{ number_format($purchase->gst_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold text-primary">Rs. {{ number_format($purchase->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection