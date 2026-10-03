@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Add Purchase (Stock In)</h2>
        <a href="{{ route('admin.purchases.index') }}" class="text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to Purchases</a>
    </div>
</div>

<form action="{{ route('admin.purchases.store') }}" method="POST">
    @csrf
    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Supplier & Invoice Details</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->company_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Invoice Number</label>
                    <input type="text" name="invoice_number" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" id="addProductBtn"><i class="fa-solid fa-plus"></i> Add Row</button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="productsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">Product</th>
                            <th width="15%">Unit Price</th>
                            <th width="15%">Quantity</th>
                            <th width="20%">Subtotal</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseItems">
                        <tr>
                            <td>
                                <select name="products[]" class="form-select product-select" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="prices[]" class="form-control price-input" value="0" required></td>
                            <td><input type="number" name="quantities[]" class="form-control qty-input" value="1" required></td>
                            <td><input type="text" class="form-control subtotal-input" readonly value="0"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fa-solid fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6 text-end fw-bold">Subtotal:</div>
                        <div class="col-6"><input type="number" step="0.01" name="subtotal" id="finalSubtotal" class="form-control form-control-sm" readonly value="0"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-end fw-bold">Discount:</div>
                        <div class="col-6"><input type="number" step="0.01" name="discount" id="discountInput" class="form-control form-control-sm" value="0"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-end fw-bold">Tax:</div>
                        <div class="col-6"><input type="number" step="0.01" name="tax" id="taxInput" class="form-control form-control-sm" value="0"></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-end fw-bold fs-5">Grand Total:</div>
                        <div class="col-6"><input type="number" step="0.01" name="grand_total" id="grandTotal" class="form-control form-control-sm fw-bold fs-5" readonly value="0"></div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-6 text-end fw-bold text-success">Paid Amount:</div>
                        <div class="col-6"><input type="number" step="0.01" name="paid_amount" id="paidAmount" class="form-control form-control-sm border-success" value="0"></div>
                    </div>
                    <div class="row">
                        <div class="col-6 text-end fw-bold text-danger">Due Amount:</div>
                        <div class="col-6"><input type="number" step="0.01" name="due_amount" id="dueAmount" class="form-control form-control-sm border-danger" readonly value="0"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fs-5 fw-bold">Submit Purchase & Update Stock</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        // Add Row
        $('#addProductBtn').click(function() {
            var row = $('#purchaseItems tr:first').clone();
            row.find('input').val(0);
            row.find('.qty-input').val(1);
            row.find('select').val('');
            $('#purchaseItems').append(row);
        });

        // Remove Row
        $(document).on('click', '.remove-row', function() {
            if ($('#purchaseItems tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            }
        });

        // Calculate Row Subtotal
        $(document).on('input', '.price-input, .qty-input', function() {
            var row = $(this).closest('tr');
            var price = parseFloat(row.find('.price-input').val()) || 0;
            var qty = parseInt(row.find('.qty-input').val()) || 0;
            row.find('.subtotal-input').val((price * qty).toFixed(2));
            calculateTotals();
        });

        // Calculate Grand Totals
        $(document).on('input', '#discountInput, #taxInput, #paidAmount', function() {
            calculateTotals();
        });

        function calculateTotals() {
            var subtotal = 0;
            $('.subtotal-input').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });
            $('#finalSubtotal').val(subtotal.toFixed(2));

            var discount = parseFloat($('#discountInput').val()) || 0;
            var tax = parseFloat($('#taxInput').val()) || 0;
            
            var grandTotal = (subtotal - discount) + tax;
            $('#grandTotal').val(grandTotal.toFixed(2));

            var paid = parseFloat($('#paidAmount').val()) || 0;
            var due = grandTotal - paid;
            $('#dueAmount').val(due.toFixed(2));
        }
    });
</script>
@endpush
@endsection


