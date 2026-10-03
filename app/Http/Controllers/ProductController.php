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
        $query = Product::with(['category', 'brand', 'mainImage'])->latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
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
            'selling_price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->except(['main_image', 'gallery_images']);
        $data['slug'] = Str::slug($request->name) . '-' . time();
        $data['status'] = $request->has('status');
        $data['featured'] = $request->has('featured');
        $data['new_arrival'] = $request->has('new_arrival');
        $data['best_seller'] = $request->has('best_seller');
        $data['requires_serial'] = $request->has('requires_serial');

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
            'selling_price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data = $request->except(['main_image', 'gallery_images']);
        $data['status'] = $request->has('status');
        $data['featured'] = $request->has('featured');
        $data['new_arrival'] = $request->has('new_arrival');
        $data['best_seller'] = $request->has('best_seller');
        $data['requires_serial'] = $request->has('requires_serial');

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
