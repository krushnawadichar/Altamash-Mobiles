<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;

class FrontendController extends Controller
{
    public function home()
    {
        $featuredProducts = Product::with('mainImage')->where('status', 1)->latest()->take(8)->get();
        $categories = Category::where('status', 1)->take(6)->get();
        return view('frontend.home', compact('featuredProducts', 'categories'));
    }

    public function shop(Request $request)
    {
        $query = Product::with(['images', 'category', 'brand', 'mainImage'])->where('status', 1);

        if ($request->has('q') && $request->q != '') {
            $query->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        if ($request->has('sort')) {
            if ($request->sort == 'price_asc') $query->orderBy('selling_price', 'asc');
            elseif ($request->sort == 'price_desc') $query->orderBy('selling_price', 'desc');
            elseif ($request->sort == 'newest') $query->latest();
        } else {
            $query->latest();
        }

        $products = $query->get();
        $categories = Category::where('status', 1)->get();
        return view('frontend.shop', compact('products', 'categories'));
    }

    public function product($slug)
    {
        $product = Product::with(['images', 'category', 'brand'])->where('slug', $slug)->where('status', 1)->firstOrFail();
        $relatedProducts = Product::with('mainImage')
                                  ->where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->where('status', 1)
                                  ->take(4)
                                  ->get();
                                  
        return view('frontend.product', compact('product', 'relatedProducts'));
    }

    public function contact()
    {
        return view('frontend.contact');
    }
}
