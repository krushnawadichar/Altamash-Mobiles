@extends('layouts.app')

@section('title', 'Sale #' . $sale->invoice_number . ' - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-shopping-cart me-2 text-primary"></i> Sale Details
            <small class="text-muted fs-6">#{{ $sale->invoice_number }}</small>
        </h2>
        <div>
            <a href="{{ route('admin.sales.print', $sale->id) }}" class="btn btn-secondary" target="_blank">
                <i class="fas fa-print me-1"></i> Print
            </a>
            <a href="{{ route('admin.sales.pdf', $sale->id) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">
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
                            <td>{{ $sale->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Customer</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Sale Date</td>
                            <td>{{ $sale->sale_date->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Method</td>
                            <td>{{ ucfirst($sale->payment_method) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="fw-bold text-muted" style="width: 150px;">Payment Status</td>
                            <td>
                                @if($sale->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($sale->payment_status == 'partial')
                                    <span class="badge bg-warning">Partial</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Amount</td>
                            <td class="fw-bold">Rs. {{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Paid Amount</td>
                            <td class="text-success">Rs. {{ number_format($sale->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Due Amount</td>
                            <td class="text-danger">Rs. {{ number_format($sale->due_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($sale->notes)
                <div class="mt-2">
                    <h6 class="fw-bold text-muted">Notes</h6>
                    <p>{{ $sale->notes }}</p>
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
                                <th>Selling Price</th>
                                <th>Total</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->details as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail->sellable->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ class_basename($detail->sellable_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>Rs. {{ number_format($detail->selling_price, 2) }}</td>
                                    <td class="fw-bold">Rs. {{ number_format($detail->total, 2) }}</td>
                                    <td class="text-success">Rs. {{ number_format($detail->profit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Subtotal:</td>
                                <td class="fw-bold">Rs. {{ number_format($sale->subtotal, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Discount:</td>
                                <td class="fw-bold text-success">- Rs. {{ number_format($sale->discount, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">GST (18%):</td>
                                <td class="fw-bold">Rs. {{ number_format($sale->gst_amount, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold text-primary">Rs. {{ number_format($sale->total_amount, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total Profit:</td>
                                <td class="fw-bold text-success">Rs. {{ number_format($sale->details->sum('profit'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection