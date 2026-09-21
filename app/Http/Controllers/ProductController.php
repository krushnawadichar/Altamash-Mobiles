<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->whereColumn('current_stock', '>', 'min_stock');
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            }
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $products = $query->paginate(15)->withQueryString();
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();

        return view('admin.products.index', compact('products', 'brands', 'categories'));
    }

    public function create()
    {
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        $autoSku = 'PRD-' . strtoupper(Str::random(6));

        return view('admin.products.create', compact('brands', 'categories', 'autoSku'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|unique:products,sku|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|in:mobile,accessory,spare_part',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'model_no' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['image', 'initial_imeis']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        // If mobile with initial IMEIs provided
        if ($product->isMobile() && $request->filled('initial_imeis')) {
            $imeis = explode("\n", str_replace("\r", "", $request->initial_imeis));
            $count = 0;
            foreach ($imeis as $imei) {
                $imei = trim($imei);
                if (!empty($imei) && !ProductSerial::where('imei_1', $imei)->exists()) {
                    $serial = ProductSerial::create([
                        'product_id' => $product->id,
                        'imei_1' => $imei,
                        'purchase_price' => $product->purchase_price,
                        'selling_price' => $product->selling_price,
                        'status' => 'available',
                    ]);
                    $this->inventoryService->recordMovement(
                        product: $product,
                        transactionType: 'STOCK_ADJUSTMENT_IN',
                        quantityChange: 1,
                        referenceId: $product->id,
                        referenceType: 'Product',
                        serial: $serial,
                        unitCost: $product->purchase_price,
                        notes: 'Opening inventory IMEI intake'
                    );
                    $count++;
                }
            }
        }

        ActivityLog::log('PRODUCT_CREATED', 'products', $product->id, "Created product: {$product->name} (SKU: {$product->sku})");

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'serials', 'inventoryTransactions.user']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();

        return view('admin.products.edit', compact('product', 'brands', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:mobile,accessory,spare_part',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'model_no' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $data = $request->except(['image']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        ActivityLog::log('PRODUCT_UPDATED', 'products', $product->id, "Updated product: {$product->name}");

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->current_stock > 0) {
            return back()->with('error', 'Cannot delete product with existing stock. Please adjust stock first.');
        }

        $productName = $product->name;
        $product->delete();
        ActivityLog::log('PRODUCT_DELETED', 'products', null, "Deleted product: {$productName}");

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function barcode(Product $product)
    {
        return view('admin.products.barcode', compact('product'));
    }
}
