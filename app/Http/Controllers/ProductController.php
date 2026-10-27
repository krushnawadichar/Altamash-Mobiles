<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Ensure latest added product always shows first
        $query = Product::with(['category', 'brand', 'mainImage'])->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('barcode', 'like', '%' . $search . '%')
                  ->orWhere('model_number', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->whereColumn('stock_quantity', '<=', 'min_stock')->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        $perPage = (int)$request->get('per_page', 15);
        if ($perPage < 1 || $perPage > 100) {
            $perPage = 15;
        }

        $products = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.products._table_rows', compact('products'))->render(),
                'pagination' => $products->links('pagination::bootstrap-5')->render(),
                'count' => $products->total(),
                'from' => $products->firstItem() ?? 0,
                'to' => $products->lastItem() ?? 0,
            ]);
        }

        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'mainImage', 'images']);
        return view('admin.products.show', compact('product'));
    }

    public function checkBarcode(Request $request)
    {
        $barcode = trim($request->get('barcode', ''));
        $excludeId = $request->get('exclude_id');

        if (empty($barcode)) {
            return response()->json(['exists' => false]);
        }

        $query = Product::where('barcode', $barcode);
        if (!empty($excludeId)) {
            $query->where('id', '!=', $excludeId);
        }

        $existing = $query->first(['id', 'name', 'sku', 'selling_price']);

        if ($existing) {
            return response()->json([
                'exists' => true,
                'product' => [
                    'id' => $existing->id,
                    'name' => $existing->name,
                    'sku' => $existing->sku,
                    'selling_price' => $existing->selling_price,
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'sku' => 'nullable|string|max:100|unique:products,sku',
        ]);

        $data = $request->except(['main_image', 'gallery_images', 'save_and_new']);
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['status'] = $request->has('status');
        $data['featured'] = $request->has('featured');
        $data['new_arrival'] = $request->has('new_arrival');
        $data['best_seller'] = $request->has('best_seller');
        $data['requires_serial'] = $request->has('requires_serial');

        // Handle numeric fields gracefully without defaulting to forced 0 if submitted blank
        $data['purchase_price'] = $request->filled('purchase_price') ? (float)$request->purchase_price : 0;
        $data['selling_price'] = (float)$request->selling_price;
        $data['mrp'] = $request->filled('mrp') ? (float)$request->mrp : null;
        $data['tax'] = $request->filled('tax') ? (float)$request->tax : 0;
        $data['stock_quantity'] = $request->filled('stock_quantity') ? (int)$request->stock_quantity : 0;
        $data['min_stock'] = $request->filled('min_stock') ? (int)$request->min_stock : 5;
        $data['barcode'] = $request->filled('barcode') ? trim($request->barcode) : null;
        $data['sku'] = $request->filled('sku') ? trim($request->sku) : 'SKU-' . strtoupper(Str::random(8));

        $product = Product::create($data);

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'is_main' => true
            ]);
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_main' => false
                ]);
            }
        }

        if ($request->has('save_and_new')) {
            return redirect()->route('admin.products.create')->with('success', 'Product "' . $product->name . '" created successfully. You can scan or add the next product now.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
        ]);

        $data = $request->except(['main_image', 'gallery_images']);
        $data['status'] = $request->has('status');
        $data['featured'] = $request->has('featured');
        $data['new_arrival'] = $request->has('new_arrival');
        $data['best_seller'] = $request->has('best_seller');
        $data['requires_serial'] = $request->has('requires_serial');

        $data['purchase_price'] = $request->filled('purchase_price') ? (float)$request->purchase_price : 0;
        $data['selling_price'] = (float)$request->selling_price;
        $data['mrp'] = $request->filled('mrp') ? (float)$request->mrp : null;
        $data['tax'] = $request->filled('tax') ? (float)$request->tax : 0;
        $data['stock_quantity'] = $request->filled('stock_quantity') ? (int)$request->stock_quantity : 0;
        $data['min_stock'] = $request->filled('min_stock') ? (int)$request->min_stock : 5;
        $data['barcode'] = $request->filled('barcode') ? trim($request->barcode) : null;

        $product->update($data);

        if ($request->hasFile('main_image')) {
            // Find existing main image
            $mainImg = $product->mainImage;
            if ($mainImg) {
                Storage::disk('public')->delete($mainImg->image);
                $mainImg->update(['image' => $request->file('main_image')->store('products', 'public')]);
            } else {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $request->file('main_image')->store('products', 'public'),
                    'is_main' => true
                ]);
            }
        }

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $path,
                    'is_main' => false
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
