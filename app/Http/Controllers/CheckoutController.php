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
            return redirect()->route('shop');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'payment_method' => 'required|string' // COD or Razorpay
        ]);

        DB::beginTransaction();

        try {
            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['phone' => $request->phone],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                ]
            );

            // Calculate totals
            $subtotal = 0;
            foreach($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $tax = 0;
            $discount = 0;
            $grand_total = $subtotal + $tax - $discount;
            
            $payment_status = $request->payment_method === 'Razorpay' ? 'paid' : 'unpaid';
            $paid_amount = $request->payment_method === 'Razorpay' ? $grand_total : 0;
            $due_amount = $request->payment_method === 'Razorpay' ? 0 : $grand_total;

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
                'notes' => 'Online Order via E-Commerce',
            ]);

            foreach ($cart as $id => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ]);

                // Update stock
                $product = Product::find($id);
                if ($product) {
                    $prevStock = $product->stock_quantity;
                    $newStock = $prevStock - $item['quantity'];
                    $product->update(['stock_quantity' => $newStock]);

                    // Log inventory
                    InventoryTransaction::create([
                        'product_id' => $id,
                        'transaction_type' => 'sale',
                        'quantity' => $item['quantity'],
                        'previous_stock' => $prevStock,
                        'new_stock' => $newStock,
                        'reference' => 'Online Order #' . $sale->invoice_number,
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
            \Log::error('Checkout Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error processing order: ' . $e->getMessage())->withInput();
        }
    }

    public function success()
    {
        if(!session('invoice_number')){
            return redirect()->route('home');
        }
        return view('frontend.checkout_success');
    }
}
