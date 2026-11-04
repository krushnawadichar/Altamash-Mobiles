<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('frontend.checkout', compact('cart', 'total'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'payment_method' => 'required|string'
        ]);

        // Pre-check stock for all cart items before proceeding
        foreach ($cart as $id => $item) {
            $product = Product::find($id);
            if (!$product || $product->stock_quantity < $item['quantity']) {
                $avail = $product ? $product->stock_quantity : 0;
                $name = $product ? $product->name : ($item['name'] ?? 'Item');
                return redirect()->route('cart.index')->with('error', "Sorry, \"{$name}\" only has {$avail} unit(s) remaining in stock. Please adjust your cart.");
            }
        }

        DB::beginTransaction();

        try {
            // Find customer by phone or email
            $customer = Customer::where('phone', $request->phone)
                                ->orWhere('email', $request->email)
                                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'status' => true,
                ]);
            } else {
                $customer->update(array_filter([
                    'name' => $customer->name ?: $request->name,
                    'email' => $customer->email ?: $request->email,
                    'phone' => $customer->phone ?: $request->phone,
                    'address' => $customer->address ?: $request->address,
                    'city' => $customer->city ?: $request->city,
                    'state' => $customer->state ?: $request->state,
                ]));
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = 0;
            $discount = 0;
            $grand_total = $subtotal + $tax - $discount;
            
            $isPaidOnline = str_contains(strtolower($request->payment_method), 'razorpay') || $request->has('razorpay_payment_id');
            $payment_status = $isPaidOnline ? 'paid' : 'unpaid';
            $paid_amount = $isPaidOnline ? $grand_total : 0;
            $due_amount = $isPaidOnline ? 0 : $grand_total;

            $invoiceNumber = 'ONL-' . strtoupper(Str::random(6)) . '-' . time();

            // Create Sale
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'invoice_number' => $invoiceNumber,
                'sale_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'grand_total' => $grand_total,
                'paid_amount' => $paid_amount,
                'due_amount' => $due_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $payment_status,
                'notes' => 'Online Order via E-Commerce Store' . ($request->filled('razorpay_payment_id') ? ' (Razorpay ID: ' . $request->razorpay_payment_id . ')' : ''),
                'created_by' => auth()->id()
            ]);

            foreach ($cart as $id => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ]);

                // Update stock safely
                $product = Product::find($id);
                if ($product) {
                    $prevStock = $product->stock_quantity;
                    $newStock = max(0, $prevStock - $item['quantity']);
                    $product->update(['stock_quantity' => $newStock]);

                    // Log inventory transaction
                    InventoryTransaction::create([
                        'product_id' => $id,
                        'transaction_type' => 'sale',
                        'quantity' => $item['quantity'],
                        'previous_stock' => $prevStock,
                        'new_stock' => $newStock,
                        'reference' => 'Online Order #' . $sale->invoice_number,
                        'user_id' => auth()->id()
                    ]);
                }
            }

            $customer->increment('total_purchases', $grand_total);

            DB::commit();
            session()->forget('cart');
            
            // Redirect to success page with invoice number
            return redirect()->route('checkout.success')->with('invoice_number', $invoiceNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Checkout Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error processing order: ' . $e->getMessage())->withInput();
        }
    }

    public function success()
    {
        $invoiceNumber = session('invoice_number');
        if (!$invoiceNumber) {
            return redirect()->route('home');
        }

        $sale = Sale::with(['customer', 'items.product'])->where('invoice_number', $invoiceNumber)->first();
        return view('frontend.checkout_success', compact('sale', 'invoiceNumber'));
    }
}
