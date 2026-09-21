@extends('layouts.admin')

@section('title', 'Sale ' . $sale->invoice_no)
@section('page_title', 'Sale Invoice Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="fw-bold mb-0">Invoice: {{ $sale->invoice_no }}</h4>
            <span class="badge {{ $sale->status_badge_class }}">{{ ucfirst($sale->status) }}</span>
            <span class="badge {{ $sale->payment_badge_class }} ms-1">{{ ucfirst($sale->payment_status) }}</span>
            <small class="text-muted ms-2">{{ $sale->sale_date->format('d M Y') }}</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($sale->due_amount > 0 && $sale->status !== 'cancelled')
                <button class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                    <i class="bi bi-cash-stack me-1"></i> Collect Due Balance
                </button>
            @endif
            <a href="{{ route('admin.pos.invoice', $sale) }}" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-printer me-1"></i> Print Invoice
            </a>
            <a href="{{ route('admin.pos.pdf', $sale) }}" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
            </a>
            @if($sale->status !== 'cancelled')
                <form action="{{ route('admin.sales.cancel', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this sale? Stock and IMEIs will automatically be restored.');">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i> Cancel Sale
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.sales.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Items Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-box-seam me-1 text-primary"></i>Sold Items ({{ $sale->items->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Product Details</th>
                                <th>Rate</th>
                                <th>Quantity</th>
                                <th>Warranty</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                    <div class="text-muted small">
                                        SKU: <code>{{ $item->product->sku }}</code>
                                        @if($item->imei)
                                            &bull; <strong class="text-primary font-monospace">IMEI: {{ $item->imei }}</strong>
                                        @endif
                                    </div>
                                </td>
                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $item->quantity }}</span></td>
                                <td>{{ $item->warranty_months > 0 ? $item->warranty_months . ' Months' : 'No Warranty' }}</td>
                                <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Records -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-wallet2 me-1 text-success"></i>Payment History ({{ $sale->payments->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Transaction Ref</th>
                                <th>Collected By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sale->payments as $pmt)
                            <tr>
                                <td>{{ $pmt->payment_date->format('d M Y') }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($pmt->amount, 2) }}</td>
                                <td><span class="badge bg-light text-dark border text-uppercase">{{ $pmt->payment_method }}</span></td>
                                <td>{{ $pmt->transaction_ref ?: '-' }}</td>
                                <td><small class="text-muted">{{ $pmt->creator?->name ?? 'System' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-2">No payments logged yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Col: Financial Summary & Customer -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-receipt me-1 text-secondary"></i>Financial Summary</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold">₹{{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    @if($sale->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Discount:</span>
                        <span class="fw-semibold">- ₹{{ number_format($sale->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($sale->tax_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">GST / Tax:</span>
                        <span class="fw-semibold">₹{{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold fs-5">Grand Total:</span>
                        <span class="fw-bold fs-5 text-primary">₹{{ number_format($sale->grand_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>Paid Amount:</span>
                        <span class="fw-bold">₹{{ number_format($sale->paid_amount, 2) }}</span>
                    </div>
                    @if($sale->due_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span class="fw-bold">Balance Due:</span>
                        <span class="fw-bold fs-6">₹{{ number_format($sale->due_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($sale->change_amount > 0)
                    <div class="d-flex justify-content-between mb-2 text-secondary">
                        <span>Change Returned:</span>
                        <span>₹{{ number_format($sale->change_amount, 2) }}</span>
                    </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <div class="pt-2 border-top mt-2">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Estimated Profit on Sale:</span>
                            <strong class="text-success">₹{{ number_format($sale->profit, 2) }}</strong>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-person me-1 text-secondary"></i>Customer Information</span>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1">{{ $sale->customer_name ?: 'Walk-in Customer' }}</h6>
                    @if($sale->customer_mobile)
                        <div class="small mb-1"><i class="bi bi-telephone me-1 text-muted"></i>+91 {{ $sale->customer_mobile }}</div>
                    @endif
                    @if($sale->customer?->address)
                        <div class="small mb-2"><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $sale->customer->address }}</div>
                    @endif
                    @if($sale->customer)
                        <div class="small pt-2 border-top">
                            <span class="text-muted">Customer Total Balance:</span>
                            <strong class="{{ $sale->customer->current_balance > 0 ? 'text-danger' : 'text-success' }}">
                                ₹{{ number_format($sale->customer->current_balance, 2) }}
                            </strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Collect Payment Modal -->
@if($sale->due_amount > 0)
<div class="modal fade" id="collectPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.sales.payment', $sale) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Collect Outstanding Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 small mb-3">
                        Remaining Due for Invoice <strong>{{ $sale->invoice_no }}</strong>: <strong>₹{{ number_format($sale->due_amount, 2) }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Amount Received (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold" max="{{ $sale->due_amount }}" value="{{ $sale->due_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" selected>Cash</option>
                            <option value="upi">UPI (GPay / PhonePe / Paytm)</option>
                            <option value="card">Card (Debit / Credit)</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Transaction Reference / UTR</label>
                        <input type="text" name="transaction_ref" class="form-control" placeholder="e.g. UPI/12345678">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Payment receipt notes...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
