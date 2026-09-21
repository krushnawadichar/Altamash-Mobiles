<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category', 'serials'])->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'in_stock') {
                $query->whereColumn('current_stock', '>', 'min_stock');
            } elseif ($request->status === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
            } elseif ($request->status === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            }
        }

        $products = $query->paginate(15)->withQueryString();
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();

        return view('admin.inventory.index', compact('products', 'brands', 'categories'));
    }

    public function imeis(Request $request)
    {
        $query = ProductSerial::with(['product.brand', 'saleItem.sale', 'purchaseItem.purchase'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('imei_1', 'like', "%{$s}%")
                  ->orWhere('imei_2', 'like', "%{$s}%")
                  ->orWhere('serial_no', 'like', "%{$s}%")
                  ->orWhereHas('product', function ($p) use ($s) {
                      $p->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $serials = $query->paginate(20)->withQueryString();

        return view('admin.inventory.imeis', compact('serials'));
    }

    public function transactions(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'serial', 'user'])->latest();

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('admin.inventory.transactions', compact('transactions'));
    }
}
