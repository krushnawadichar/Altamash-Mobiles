@extends('layouts.admin')

@section('title', 'Purchase Order ' . $purchase->purchase_no)
@section('page_title', 'Purchase Details')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Purchase Order: {{ $purchase->purchase_no }}</h4>
            <small class="text-muted">Supplier: {{ $purchase->supplier->name }} &bull; Date: {{ $purchase->purchase_date->format('d M Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            @if($purchase->due_amount > 0)
                <button class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="bi bi-cash-stack me-1"></i> Pay Outstanding Due
                </button>
            @endif
            <a href="{{ route('admin.purchases.index') }}" class="btn btn-light border btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-3">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-box-seam me-1 text-primary"></i>Purchased Products ({{ $purchase->items->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Cost Price</th>
                                <th>Quantity</th>
                                <th>Tax</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>

                                    <!-- If Mobiles, list IMEIs -->
                                    @if($item->serials->count() > 0)
                                        <div class="mt-2">
                                            <small class="fw-semibold text-primary d-block">Registered IMEIs:</small>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($item->serials as $serial)
                                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;">
                                                        {{ $serial->imei_1 }} ({{ $serial->status }})
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>₹{{ number_format($item->purchase_price, 2) }}</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $item->quantity }}</span></td>
                                <td>{{ $item->tax_percent }}%</td>
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
                    <span class="fw-bold"><i class="bi bi-wallet2 me-1 text-success"></i>Supplier Payments ({{ $purchase->payments->count() }})</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Ref No</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->payments as $pmt)
                            <tr>
                                <td>{{ $pmt->payment_date->format('d M Y') }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($pmt->amount, 2) }}</td>
                                <td><span class="badge bg-light text-dark border">{{ strtoupper($pmt->payment_method) }}</span></td>
                                <td>{{ $pmt->reference_no ?: '-' }}</td>
                                <td><small class="text-muted">{{ $pmt->creator?->name ?? 'System' }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-2">No payments recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-receipt me-1 text-secondary"></i>Financial Summary</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold">₹{{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tax Amount:</span>
                        <span class="fw-semibold">₹{{ number_format($purchase->tax_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Discount:</span>
                        <span class="fw-semibold text-danger">- ₹{{ number_format($purchase->discount_amount, 2) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold fs-5">Grand Total:</span>
                        <span class="fw-bold fs-5 text-dark">₹{{ number_format($purchase->grand_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success fw-semibold">Paid Amount:</span>
                        <span class="text-success fw-bold">₹{{ number_format($purchase->paid_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-danger fw-semibold">Outstanding Due:</span>
                        <span class="text-danger fw-bold fs-6">₹{{ number_format($purchase->due_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="text-muted small">Status:</span>
                        <span class="badge {{ $purchase->payment_badge_class }}">{{ ucfirst($purchase->payment_status) }}</span>
                    </div>
                </div>
            </div>

            <!-- Supplier Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-truck me-1 text-secondary"></i>Supplier Details</span>
                </div>
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-1">{{ $purchase->supplier->name }}</h6>
                    <p class="text-muted small mb-2">{{ $purchase->supplier->company_name ?: '' }}</p>
                    <div class="small mb-1"><i class="bi bi-telephone me-1 text-muted"></i>{{ $purchase->supplier->mobile }}</div>
                    <div class="small mb-1"><i class="bi bi-envelope me-1 text-muted"></i>{{ $purchase->supplier->email ?: '-' }}</div>
                    <div class="small mb-2"><i class="bi bi-geo-alt me-1 text-muted"></i>{{ $purchase->supplier->address ?: '-' }}</div>
                    <div class="small"><span class="text-muted">Supplier Total Due:</span> <strong class="text-danger">₹{{ number_format($purchase->supplier->current_balance, 2) }}</strong></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Payment Modal -->
@if($purchase->due_amount > 0)
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.purchases.payment', $purchase) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Record Supplier Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 small mb-3">
                        Remaining Due for this purchase: <strong>₹{{ number_format($purchase->due_amount, 2) }}</strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control fw-bold" max="{{ $purchase->due_amount }}" value="{{ $purchase->due_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer" selected>Bank Transfer (RTGS/NEFT)</option>
                            <option value="upi">UPI / QR Code</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Transaction Reference / UTR</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="e.g. UTR12345678">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="Payment remarks...">
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
