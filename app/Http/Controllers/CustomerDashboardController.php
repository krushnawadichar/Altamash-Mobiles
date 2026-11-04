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
        
        $orders = Sale::where(function ($q) use ($customer, $user) {
            if ($customer) {
                $q->where('customer_id', $customer->id);
            }
            $q->orWhere('created_by', $user->id);
        })
        ->with('items.product')
        ->latest('id')
        ->get();

        return view('dashboard', compact('user', 'customer', 'orders'));
    }
}
