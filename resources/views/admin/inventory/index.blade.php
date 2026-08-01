@extends('layouts.app')

@section('title', 'Inventory - Altamash Mobiles')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-warehouse me-2 text-primary"></i> Inventory Management
        </h2>
    </div>

    <!-- Low Stock Alerts -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-0">
                    <h5 class="fw-bold mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Products
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Min Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts ?? [] as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-danger fw-bold">{{ $product->current_stock }}</td>
                                        <td>{{ $product->minimum_stock }}</td>
                                        <td>
                                            <span class="badge bg-warning">Low Stock</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            All products have sufficient stock
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 border-0">
                    <h5 class="fw-bold mb-0 text-danger">
                        <i class="fas fa-times-circle me-2"></i> Out of Stock Products
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outOfStockProducts ?? [] as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td class="text-danger fw-bold">{{ $product->current_stock }}</td>
                                        <td>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            No products out of stock
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Accessories -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning bg-opacity-10 border-0">
                    <h5 class="fw-bold mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Low Stock Accessories
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Accessory</th>
                                    <th>Current Stock</th>
                                    <th>Min Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockAccessories ?? [] as $accessory)
                                    <tr>
                                        <td>{{ $accessory->name }}</td>
                                        <td class="text-danger fw-bold">{{ $accessory->current_stock }}</td>
                                        <td>{{ $accessory->minimum_stock }}</td>
                                        <td>
                                            <span class="badge bg-warning">Low Stock</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            All accessories have sufficient stock
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger bg-opacity-10 border-0">
                    <h5 class="fw-bold mb-0 text-danger">
                        <i class="fas fa-times-circle me-2"></i> Out of Stock Accessories
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Accessory</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outOfStockAccessories ?? [] as $accessory)
                                    <tr>
                                        <td>{{ $accessory->name }}</td>
                                        <td class="text-danger fw-bold">{{ $accessory->current_stock }}</td>
                                        <td>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            No accessories out of stock
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Adjustment -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-edit me-2"></i> Stock Adjustment
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventory.adjustment') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item Type</label>
                            <select class="form-select" name="item_type" id="adjustment_type" required>
                                <option value="">Select Type</option>
                                <option value="App\Models\Product">Product</option>
                                <option value="App\Models\Accessory">Accessory</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Item</label>
                            <select class="form-select" name="item_id" id="adjustment_item" required>
                                <option value="">Select Item</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity</label>
                            <input type="number" class="form-control" name="quantity" 
                                   placeholder="+/- Quantity" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Remarks</label>
                            <input type="text" class="form-control" name="remarks" 
                                   placeholder="Optional notes">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-1"></i> Adjust
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stock Transfer -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-exchange-alt me-2"></i> Stock Transfer
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.inventory.transfer') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">From Type</label>
                            <select class="form-select" name="source_type" id="transfer_from_type" required>
                                <option value="">Select Type</option>
                                <option value="App\Models\Product">Product</option>
                                <option value="App\Models\Accessory">Accessory</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">From Item</label>
                            <select class="form-select" name="source_id" id="transfer_from_item" required>
                                <option value="">Select Item</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">To Type</label>
                            <select class="form-select" name="destination_type" id="transfer_to_type" required>
                                <option value="">Select Type</option>
                                <option value="App\Models\Product">Product</option>
                                <option value="App\Models\Accessory">Accessory</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">To Item</label>
                            <select class="form-select" name="destination_id" id="transfer_to_item" required>
                                <option value="">Select Item</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Quantity</label>
                            <input type="number" class="form-control" name="quantity" 
                                   placeholder="Qty" required min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Remarks</label>
                            <input type="text" class="form-control" name="remarks" 
                                   placeholder="Optional notes">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-exchange-alt me-1"></i> Transfer
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Damage & Lost Stock -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-times-circle me-2"></i> Damage Stock
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.damage') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Item Type</label>
                                    <select class="form-select" name="item_type" required>
                                        <option value="">Select</option>
                                        <option value="App\Models\Product">Product</option>
                                        <option value="App\Models\Accessory">Accessory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Item</label>
                                    <select class="form-select" name="item_id" required>
                                        <option value="">Select Item</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Qty</label>
                                    <input type="number" class="form-control" name="quantity" 
                                           placeholder="Qty" required min="1">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Remarks</label>
                                    <input type="text" class="form-control" name="remarks" 
                                           placeholder="Damage description">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-save me-1"></i> Record
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-search-minus me-2"></i> Lost Stock
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.inventory.lost') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Item Type</label>
                                    <select class="form-select" name="item_type" required>
                                        <option value="">Select</option>
                                        <option value="App\Models\Product">Product</option>
                                        <option value="App\Models\Accessory">Accessory</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Item</label>
                                    <select class="form-select" name="item_id" required>
                                        <option value="">Select Item</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Qty</label>
                                    <input type="number" class="form-control" name="quantity" 
                                           placeholder="Qty" required min="1">
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-2">
                                    <label class="form-label fw-bold">Remarks</label>
                                    <input type="text" class="form-control" name="remarks" 
                                           placeholder="Loss description">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    <i class="fas fa-save me-1"></i> Record
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-history me-2"></i> Inventory History
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories ?? [] as $inventory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $inventory->inventoriable->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($inventory->type) }}
                                    </span>
                                </td>
                                <td class="{{ $inventory->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $inventory->quantity }}
                                </td>
                                <td>Rs. {{ number_format($inventory->price, 0) }}</td>
                                <td>Rs. {{ number_format($inventory->total_price, 0) }}</td>
                                <td>{{ $inventory->created_at->format('d M, Y H:i') }}</td>
                                <td>{{ $inventory->creator->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">No inventory records found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Stock Adjustment - Load items
    $('#adjustment_type').change(function() {
        const type = $(this).val();
        const select = $('#adjustment_item');
        loadItems(type, select);
    });

    // Transfer - Load From items
    $('#transfer_from_type').change(function() {
        const type = $(this).val();
        const select = $('#transfer_from_item');
        loadItems(type, select);
    });

    // Transfer - Load To items
    $('#transfer_to_type').change(function() {
        const type = $(this).val();
        const select = $('#transfer_to_item');
        loadItems(type, select);
    });

    // Damage - Load items
    $('form[action*="damage"] select[name="item_type"]').change(function() {
        const type = $(this).val();
        const select = $(this).closest('form').find('select[name="item_id"]');
        loadItems(type, select);
    });

    // Lost - Load items
    $('form[action*="lost"] select[name="item_type"]').change(function() {
        const type = $(this).val();
        const select = $(this).closest('form').find('select[name="item_id"]');
        loadItems(type, select);
    });

    function loadItems(type, selectElement) {
        selectElement.html('<option value="">Loading...</option>');

        if (type) {
            $.ajax({
                url: '{{ route("admin.inventory.get-items") }}',
                method: 'GET',
                data: { type: type },
                success: function(response) {
                    selectElement.html('<option value="">Select Item</option>');
                    if (response.length === 0) {
                        selectElement.append('<option value="">No items found</option>');
                    } else {
                        $.each(response, function(key, value) {
                            selectElement.append(
                                `<option value="${value.id}">${value.name} (${value.sku}) - Stock: ${value.current_stock}</option>`
                            );
                        });
                    }
                },
                error: function() {
                    selectElement.html('<option value="">Error loading items</option>');
                }
            });
        } else {
            selectElement.html('<option value="">Select Type First</option>');
        }
    }
});
</script>
@endpush
@endsection