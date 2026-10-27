@forelse($products as $product)
<tr>
    <td><span class="text-muted fw-semibold">#{{ $product->id }}</span></td>
    <td>
        @if($product->mainImage)
            @php $img = $product->mainImage->image; @endphp
            <img src="{{ str_starts_with($img, 'http') ? $img : Storage::url($img) }}" 
                 alt="Image" 
                 width="48" 
                 height="48" 
                 class="img-thumbnail rounded" 
                 style="object-fit: cover;">
        @else
            <div class="bg-light border rounded d-flex align-items-center justify-content-center text-muted" style="width: 48px; height: 48px;">
                <i class="fa-regular fa-image small"></i>
            </div>
        @endif
    </td>
    <td>
        <a href="{{ route('admin.products.show', $product) }}" class="fw-bold text-dark text-decoration-none d-block hover-primary">
            {{ $product->name }}
        </a>
        <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
            <span class="badge bg-light text-secondary border font-monospace">SKU: {{ $product->sku ?? 'N/A' }}</span>
            @if($product->barcode)
                <span class="badge bg-dark-subtle text-dark border font-monospace" title="Barcode">
                    <i class="fa-solid fa-barcode me-1 text-primary"></i>{{ $product->barcode }}
                </span>
            @endif
            @if($product->requires_serial)
                <span class="badge bg-primary-subtle text-primary border" title="Requires IMEI/Serial">
                    <i class="fa-solid fa-mobile-screen-button"></i> IMEI
                </span>
            @endif
        </div>
    </td>
    <td>
        <span class="badge bg-light text-dark border">{{ $product->category->name ?? 'Uncategorized' }}</span>
    </td>
    <td>{{ $product->brand->name ?? '-' }}</td>
    <td>
        <span class="fw-bold text-dark">₹{{ number_format($product->selling_price, 2) }}</span>
        @if($product->mrp && $product->mrp > $product->selling_price)
            <small class="text-muted text-decoration-line-through d-block">₹{{ number_format($product->mrp, 2) }}</small>
        @endif
    </td>
    <td>
        @if($product->stock_quantity <= 0)
            <span class="badge bg-danger">Out of Stock (0)</span>
        @elseif($product->stock_quantity <= $product->min_stock)
            <span class="badge bg-warning text-dark">{{ $product->stock_quantity }} (Low)</span>
        @else
            <span class="badge bg-success">{{ $product->stock_quantity }}</span>
        @endif
    </td>
    <td>
        @if($product->status)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>
    <td>
        <div class="d-flex gap-1">
            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info" title="View Product Details">
                <i class="fa-solid fa-eye"></i>
            </a>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="Edit Product">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center py-5">
        <div class="text-muted">
            <i class="fa-solid fa-magnifying-glass fs-1 d-block mb-3 text-secondary"></i>
            <h5>No products found</h5>
            <p class="small mb-0">Try scanning a different barcode or clearing your search filters.</p>
        </div>
    </td>
</tr>
@endforelse
