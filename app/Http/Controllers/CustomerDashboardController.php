<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        $orders = [];
        if ($customer) {
            $orders = Sale::where('customer_id', $customer->id)->with('items.product')->latest()->get();
        }

        return view('dashboard', compact('user', 'customer', 'orders'));
    }
}
