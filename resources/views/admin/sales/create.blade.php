@extends('layouts.app')

@section('title', 'Create Sale - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-plus-circle me-2 text-primary"></i> Create Sale
        </h2>
        <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.sales.store') }}" method="POST" id="saleForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label fw-bold">Customer</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" 
                                    id="customer_id" name="customer_id">
                                <option value="">Walk-in Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} - {{ $customer->phone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sale_date" class="form-label fw-bold">Sale Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('sale_date') is-invalid @enderror" 
                                   id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                            @error('sale_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" 
                                    id="payment_method" name="payment_method" required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                            </select>
                            @error('payment_method')
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
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Items <span class="text-danger">*</span></label>
                    <div id="itemsContainer">
                        <div class="item-row mb-2">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-select item-type" name="items[0][type]" required>
                                        <option value="">Select Type</option>
                                        <option value="product">Product</option>
                                        <option value="accessory">Accessory</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select item-id" name="items[0][id]" required>
                                        <option value="">Select Item</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control item-quantity" 
                                           name="items[0][quantity]" placeholder="Qty" required min="1">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.01" class="form-control item-price" 
                                           name="items[0][selling_price]" placeholder="Price" required min="0">
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
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Sale
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

    $('#addItem').click(function() {
        const row = `
            <div class="item-row mb-2">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-select item-type" name="items[${itemCount}][type]" required>
                            <option value="">Select Type</option>
                            <option value="product">Product</option>
                            <option value="accessory">Accessory</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select item-id" name="items[${itemCount}][id]" required>
                            <option value="">Select Item</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control item-quantity" 
                               name="items[${itemCount}][quantity]" placeholder="Qty" required min="1">
                    </div>
                    <div class="col-md-2">
                        <input type="number" step="0.01" class="form-control item-price" 
                               name="items[${itemCount}][selling_price]" placeholder="Price" required min="0">
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

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        } else {
            alert('You must have at least one item.');
        }
    });

    $(document).on('change', '.item-type', function() {
        const type = $(this).val();
        const select = $(this).closest('.row').find('.item-id');
        select.html('<option value="">Loading...</option>');

        if (type) {
            $.ajax({
                url: '{{ route("admin.sales.get-items") }}',
                method: 'GET',
                data: { type: type },
                success: function(response) {
                    select.html('<option value="">Select Item</option>');
                    $.each(response, function(key, value) {
                        select.append(`<option value="${value.id}">${value.name} (${value.sku}) - Rs. ${value.selling_price}</option>`);
                    });
                }
            });
        } else {
            select.html('<option value="">Select Type First</option>');
        }
    });

    // Auto-fill price when item selected
    $(document).on('change', '.item-id', function() {
        const select = $(this);
        const priceInput = $(this).closest('.row').find('.item-price');
        const selectedOption = select.find('option:selected');
        const priceText = selectedOption.text().match(/Rs\. ([\d,]+)/);
        
        if (priceText) {
            priceInput.val(priceText[1].replace(/,/g, ''));
        }
    });
});
</script>
@endpush
@endsection