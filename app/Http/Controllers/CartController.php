<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return view('frontend.cart', compact('cart', 'total'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::with(['images', 'mainImage'])->findOrFail($id);
        $cart = session()->get('cart', []);
        
        $qty = max(1, (int)$request->input('quantity', 1));
        $currentCartQty = isset($cart[$id]) ? (int)$cart[$id]['quantity'] : 0;
        $totalQty = $currentCartQty + $qty;

        if ($product->stock_quantity < $totalQty) {
            $availableToAdd = max(0, $product->stock_quantity - $currentCartQty);
            if ($availableToAdd <= 0) {
                return back()->with('error', 'You already have the maximum available stock (' . $product->stock_quantity . ') of "' . $product->name . '" in your cart.');
            }
            return back()->with('error', 'Cannot add ' . $qty . ' more. Only ' . $availableToAdd . ' unit(s) remaining in stock.');
        }

        $img = $product->mainImage ? $product->mainImage->image : ($product->images->first()?->image ?? null);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $totalQty;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $qty,
                "price" => (float)$product->selling_price,
                "image" => $img
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', '"' . $product->name . '" added to cart successfully!');
    }

    public function update(Request $request)
    {
        if ($request->id && $request->has('quantity')) {
            $cart = session()->get('cart', []);
            $newQty = (int)$request->quantity;

            if ($newQty <= 0) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
                return redirect()->back()->with('success', 'Item removed from cart.');
            }
            
            // Check stock
            $product = Product::find($request->id);
            if ($product && $product->stock_quantity >= $newQty) {
                $cart[$request->id]["quantity"] = $newQty;
                session()->put('cart', $cart);
                session()->flash('success', 'Cart updated successfully.');
            } else {
                $available = $product ? $product->stock_quantity : 0;
                session()->flash('error', 'Cannot set quantity to ' . $newQty . '. Only ' . $available . ' available in stock.');
            }
        }
        return redirect()->back();
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart', []);
            if (isset($cart[$request->id])) {
                $name = $cart[$request->id]['name'] ?? 'Product';
                unset($cart[$request->id]);
                session()->put('cart', $cart);
                session()->flash('success', '"' . $name . '" removed from cart.');
            }
        }
        return redirect()->back();
    }
}
