@extends('layouts.admin')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold mb-1">
            <i class="fa-solid fa-file-invoice text-primary me-2"></i>Purchase Bill: {{ $purchase->invoice_number ?? ('#' . $purchase->id) }}
        </h2>
        <a href="{{ route('admin.purchases.index') }}" class="text-decoration-none">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Purchases
        </a>
    </div>
    <div class="col-md-6 text-md-end mt-2 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
        @if($purchase->due_amount > 0)
            <button type="button" class="btn btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paySupplierDueModal">
                <i class="fa-solid fa-hand-holding-dollar me-1"></i> Pay Supplier Due
            </button>
        @endif
        <a href="{{ route('admin.purchases.print', $purchase) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="fa-solid fa-print me-1"></i> Print Bill
        </a>
    </div>
</div>

<!-- Pending Due or Settled Notice -->
@if($purchase->due_amount > 0)
    <div class="alert alert-warning border-warning shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <i class="fa-solid fa-triangle-exclamation text-danger fs-5 me-2"></i>
            <strong>Pending Supplier Due:</strong> Outstanding balance of 
            <span class="fw-bold text-danger fs-5">₹{{ number_format($purchase->due_amount, 2) }}</span> is owed to 
            <strong>{{ $purchase->supplier->name }}</strong>.
        </div>
        <button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#paySupplierDueModal">
            <i class="fa-solid fa-money-bill-wave me-1"></i> Pay Due Amount
        </button>
    </div>
@else
    <div class="alert alert-success border-success shadow-sm mb-4">
        <i class="fa-solid fa-circle-check fs-5 me-2"></i>
        <strong>Paid in Full:</strong> This purchase bill has been completely settled. No outstanding balance.
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Supplier Info -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title fw-bold m-0"><i class="fa-solid fa-truck me-2 text-primary"></i>Supplier Details</h5>
            </div>
            <div class="card-body">
                <h5 class="fw-bold text-dark mb-1">{{ $purchase->supplier->name }}</h5>
                @if($purchase->supplier->company_name)
                    <p class="text-muted small mb-2"><i class="fa-solid fa-building me-1"></i>{{ $purchase->supplier->company_name }}</p>
                @endif
                <hr class="my-2">
                <p class="mb-1"><strong>Phone:</strong> {{ $purchase->supplier->phone ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $purchase->supplier->email ?? 'N/A' }}</p>
                @if($purchase->supplier->gst_number)
                    <p class="mb-1"><strong>GST No:</strong> <span class="font-monospace">{{ $purchase->supplier->gst_number }}</span></p>
                @endif
                @if($purchase->supplier->address)
                    <p class="mb-0 text-muted small mt-2"><i class="fa-solid fa-location-dot me-1"></i>{{ $purchase->supplier->address }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Purchase Info -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title fw-bold m-0"><i class="fa-solid fa-receipt me-2 text-primary"></i>Bill Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Invoice / Bill No:</strong> <span class="font-monospace fw-bold">{{ $purchase->invoice_number ?? 'N/A' }}</span></p>
                <p class="mb-2"><strong>Purchase Date:</strong> {{ $purchase->purchase_date->format('d M, Y') }}</p>
                <p class="mb-2"><strong>Recorded By:</strong> {{ $purchase->creator->name ?? 'Admin' }}</p>
                <p class="mb-0"><strong>Total Items:</strong> <span class="badge bg-secondary">{{ $purchase->items->count() }} Items ({{ $purchase->items->sum('quantity') }} Units)</span></p>
            </div>
        </div>
    </div>

    <!-- Payment Status -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100 bg-light">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold m-0"><i class="fa-solid fa-wallet me-2 text-primary"></i>Payment Summary</h5>
                <div>
                    @if($purchase->payment_status == 'paid')
                        <span class="badge bg-success fs-6">Paid in Full</span>
                    @elseif($purchase->payment_status == 'partial')
                        <span class="badge bg-warning text-dark fs-6">Partial Paid</span>
                    @else
                        <span class="badge bg-danger fs-6">Unpaid / Due</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Grand Total:</span>
                    <span class="fw-bold fs-5 text-primary">₹{{ number_format($purchase->grand_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Paid to Date:</span>
                    <span class="fw-bold text-success fs-6">₹{{ number_format($purchase->paid_amount, 2) }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-muted' }}">Outstanding Due:</span>
                    <span class="fw-bolder fs-4 {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-muted' }}">₹{{ number_format($purchase->due_amount, 2) }}</span>
                </div>
                @if($purchase->due_amount > 0)
                    <button type="button" class="btn btn-warning w-100 fw-bold mt-3 shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#paySupplierDueModal">
                        <i class="fa-solid fa-hand-holding-dollar me-1"></i> Record Due Payment
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Items Purchased -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i>Items Purchased</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>SKU / Barcode</th>
                        <th class="text-end">Purchase Price</th>
                        <th class="text-center">Qty Received</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $item->product->name ?? 'Product Deleted' }}</td>
                        <td class="font-monospace small text-muted">
                            {{ $item->product->sku ?? '-' }}
                            @if($item->product->barcode)
                                | {{ $item->product->barcode }}
                            @endif
                        </td>
                        <td class="text-end fw-semibold">₹{{ number_format($item->purchase_price, 2) }}</td>
                        <td class="text-center fw-bold">{{ $item->quantity }}</td>
                        <td class="text-end fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-top">
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">Subtotal</td>
                        <td class="text-end fw-semibold">₹{{ number_format($purchase->subtotal, 2) }}</td>
                    </tr>
                    @if($purchase->discount > 0)
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">Discount</td>
                        <td class="text-end text-danger fw-semibold">- ₹{{ number_format($purchase->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($purchase->tax > 0)
                    <tr>
                        <td colspan="5" class="text-end fw-semibold">Tax (GST)</td>
                        <td class="text-end text-success fw-semibold">+ ₹{{ number_format($purchase->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="table-light">
                        <td colspan="5" class="text-end fw-bold fs-5">Grand Total</td>
                        <td class="text-end fw-bold fs-5 text-primary">₹{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Notes & Payment Audit Trail -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-history me-2 text-primary"></i>Notes & Payment History Log</h5>
    </div>
    <div class="card-body">
        @if($purchase->notes)
            <div class="p-3 bg-light rounded border font-monospace small" style="white-space: pre-wrap; line-height: 1.6;">{{ $purchase->notes }}</div>
        @else
            <p class="text-muted mb-0 fst-italic">No additional notes or payments recorded yet.</p>
        @endif
    </div>
</div>

<!-- Modal: Pay Supplier Due -->
@if($purchase->due_amount > 0)
<div class="modal fade" id="paySupplierDueModal" tabindex="-1" aria-labelledby="paySupplierDueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.purchases.pay-due', $purchase) }}" method="POST" id="paySupplierDueForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="paySupplierDueModalLabel">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i>Pay Supplier Due (Bill Payment)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Due Balance Card -->
                    <div class="p-3 bg-light rounded border mb-3 text-center">
                        <span class="text-muted small d-block">Current Pending Due to Supplier</span>
                        <span class="fs-2 fw-bold text-danger">₹{{ number_format($purchase->due_amount, 2) }}</span>
                        <div class="small text-muted mt-1">
                            Supplier: <strong>{{ $purchase->supplier->name }}</strong> &bull; Bill: <strong>{{ $purchase->invoice_number ?? '#' . $purchase->id }}</strong>
                        </div>
                    </div>

                    <!-- Payment Amount Input -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white fw-bold">₹</span>
                            <input type="text" 
                                   inputmode="decimal" 
                                   name="amount" 
                                   id="supplierPayAmountInput"
                                   class="form-control form-control-lg fw-bold number-only border-primary" 
                                   value="{{ $purchase->due_amount }}" 
                                   max="{{ $purchase->due_amount }}"
                                   required 
                                   autocomplete="off">
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-success py-1 px-2 fw-semibold" onclick="$('#supplierPayAmountInput').val('{{ $purchase->due_amount }}')">
                                Full Amount (₹{{ number_format($purchase->due_amount, 2) }})
                            </button>
                            @if($purchase->due_amount > 100)
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="$('#supplierPayAmountInput').val('{{ round($purchase->due_amount / 2, 2) }}')">
                                    50% (₹{{ number_format(round($purchase->due_amount / 2, 2), 2) }})
                                </button>
                            @endif
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="$('#supplierPayAmountInput').val('').focus()">
                                Clear
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">Enter the exact amount paid to this vendor.</small>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method (How you paid) <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select form-select-lg" required>
                            <option value="Bank Transfer / NEFT / RTGS" selected>🏦 Bank Transfer / NEFT / RTGS</option>
                            <option value="UPI / QR">📱 UPI / QR (PhonePe / GPay / Paytm)</option>
                            <option value="Cheque">📑 Cheque</option>
                            <option value="Cash">💵 Cash</option>
                            <option value="Debit / Credit Card">💳 Debit / Credit Card</option>
                        </select>
                    </div>

                    <!-- Reference / UTR Number -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Transaction Reference / Cheque No / UTR (Optional)</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="e.g. UTR40281928, Cheque #004521">
                    </div>

                    <!-- Notes -->
                    <div class="mb-2">
                        <label class="form-label fw-bold">Payment Note (Optional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Paid from HDFC Current A/C">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="fa-solid fa-check-circle me-1"></i> Confirm Payment & Update Due
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
