@extends('layouts.app')

@section('title', 'Create Purchase - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Create Purchase
        </h2>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.purchases.store') }}" method="POST" id="purchaseForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label fw-bold">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                    id="supplier_id" name="supplier_id" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }} - {{ $supplier->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="purchase_date" class="form-label fw-bold">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                   id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Items <span class="text-danger">*</span></label>
                    <div id="itemsContainer">
                        <div class="item-row mb-2">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-select item-type" name="items[0][type]" required>
                                        <option value="">Select Type</option>
                                        <option value="product">Product</option>
                                        <option value="accessory">Accessory</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select item-id" name="items[0][id]" required>
                                        <option value="">Select Item</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control item-quantity" 
                                           name="items[0][quantity]" placeholder="Qty" required min="1">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-item">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary mt-2" id="addItem">
                        <i class="fas fa-plus-circle me-1"></i> Add Item
                    </button>
                    <div id="itemsError" class="text-danger mt-2"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="discount" class="form-label fw-bold">Discount</label>
                            <input type="number" step="0.01" class="form-control @error('discount') is-invalid @enderror" 
                                   id="discount" name="discount" value="{{ old('discount', 0) }}">
                            @error('discount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="paid_amount" class="form-label fw-bold">Paid Amount</label>
                            <input type="number" step="0.01" class="form-control @error('paid_amount') is-invalid @enderror" 
                                   id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}">
                            @error('paid_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-bold">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.purchases.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let itemCount = 1;

    // Add item row
    $('#addItem').click(function() {
        const row = `
            <div class="item-row mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-select item-type" name="items[${itemCount}][type]" required>
                            <option value="">Select Type</option>
                            <option value="product">Product</option>
                            <option value="accessory">Accessory</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select item-id" name="items[${itemCount}][id]" required>
                            <option value="">Select Item</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control item-quantity" 
                               name="items[${itemCount}][quantity]" placeholder="Qty" required min="1">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-item">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#itemsContainer').append(row);
        itemCount++;
    });

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        } else {
            alert('You must have at least one item.');
        }
    });

    // Load items based on type selection
    $(document).on('change', '.item-type', function() {
        const type = $(this).val();
        const select = $(this).closest('.row').find('.item-id');
        select.html('<option value="">Loading...</option>');

        if (type) {
            $.ajax({
                url: '{{ route("admin.purchases.get-items") }}',
                method: 'GET',
                data: { type: type },
                success: function(response) {
                    select.html('<option value="">Select Item</option>');
                    $.each(response, function(key, value) {
                        select.append(`<option value="${value.id}">${value.name} (${value.sku})</option>`);
                    });
                },
                error: function() {
                    select.html('<option value="">Error loading items</option>');
                }
            });
        } else {
            select.html('<option value="">Select Type First</option>');
        }
    });
});
</script>
@endpush
@endsection