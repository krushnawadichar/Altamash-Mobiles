@extends('layouts.admin')

@section('title', 'Create Stock Adjustment')
@section('page_title', 'New Stock Adjustment')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-0">Record Stock Adjustment</h4>
            <small class="text-muted">Increase or decrease stock due to damage, loss, count correction, or discovery</small>
        </div>
        <a href="{{ route('admin.inventory.adjustments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Adjustment Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Reason for Adjustment <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="damaged">Damaged Stock</option>
                            <option value="lost">Lost / Missing</option>
                            <option value="found">Found / Discovered</option>
                            <option value="correction" selected>Physical Count Correction</option>
                            <option value="manual">Manual Adjustment</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Notes / Audit Remarks</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. End of month physical audit discrepancy">
                    </div>
                </div>

                <h6 class="fw-bold border-top pt-3 mb-2"><i class="bi bi-list-check me-1 text-primary"></i>Items to Adjust</h6>

                <div id="adjustmentItemsContainer">
                    <div class="row g-2 mb-2 item-row align-items-center">
                        <div class="col-md-5">
                            <label class="form-label small text-muted mb-1">Select Product</label>
                            <select name="items[0][product_id]" class="form-select select2" required>
                                <option value="">Choose Product...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} (Current Stock: {{ $p->current_stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Adjustment Type</label>
                            <select name="items[0][type]" class="form-select" required>
                                <option value="decrease">Decrease Stock (-)</option>
                                <option value="increase">Increase Stock (+)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-1">Quantity</label>
                            <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-1 text-end">
                            <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.item-row').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-light border btn-sm mt-2" id="addItemBtn">
                    <i class="bi bi-plus-circle me-1"></i> Add Another Item
                </button>
            </div>

            <div class="card-footer bg-white text-end py-3">
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="bi bi-check-circle-fill me-1"></i> Confirm & Adjust Stock
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    let rowIndex = 1;
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('adjustmentItemsContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 item-row align-items-center';
        row.innerHTML = `
            <div class="col-md-5">
                <select name="items[${rowIndex}][product_id]" class="form-select select2" required>
                    <option value="">Choose Product...</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Current Stock: {{ $p->current_stock }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="items[${rowIndex}][type]" class="form-select" required>
                    <option value="decrease">Decrease Stock (-)</option>
                    <option value="increase">Increase Stock (+)</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.item-row').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        $(row).find('.select2').select2({ theme: 'bootstrap-5' });
        rowIndex++;
    });
</script>
@endpush
