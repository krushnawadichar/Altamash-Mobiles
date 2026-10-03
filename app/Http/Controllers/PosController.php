<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('mainImage')->where('status', 1)->where('stock_quantity', '>', 0)->get();
        $customers = Customer::where('status', 1)->get();
        return view('admin.pos.index', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'prices' => 'required|array',
            'quantities' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $payment_status = 'unpaid';
            if ($request->paid_amount >= $request->grand_total) {
                $payment_status = 'paid';
            } elseif ($request->paid_amount > 0) {
                $payment_status = 'partial';
            }

            // Generate Invoice Number
            $invoiceNumber = 'INV-' . strtoupper(Str::random(6)) . '-' . time();

            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'invoice_number' => $invoiceNumber,
                'sale_date' => now()->toDateString(),
                'subtotal' => $request->subtotal,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'grand_total' => $request->grand_total,
                'paid_amount' => $request->paid_amount,
                'due_amount' => $request->due_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $payment_status,
                'created_by' => auth()->id()
            ]);

            foreach ($request->products as $index => $productId) {
                $price = $request->prices[$index];
                $qty = $request->quantities[$index];
                $subtotal = $price * $qty;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'subtotal' => $subtotal
                ]);

                // Update product stock
                $product = Product::find($productId);
                $prevStock = $product->stock_quantity;
                $newStock = $prevStock - $qty;
                
                $product->update([
                    'stock_quantity' => $newStock
                ]);

                // Log Transaction
                InventoryTransaction::create([
                    'product_id' => $productId,
                    'transaction_type' => 'sale',
                    'quantity' => $qty,
                    'previous_stock' => $prevStock,
                    'new_stock' => $newStock,
                    'reference' => 'Sale #' . $sale->invoice_number,
                    'user_id' => auth()->id()
                ]);
            }
            
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                $customer->increment('total_purchases', $request->grand_total);
            }

            DB::commit();
            return redirect()->route('admin.sales.show', $sale)->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing sale: ' . $e->getMessage())->withInput();
        }
    }
}
