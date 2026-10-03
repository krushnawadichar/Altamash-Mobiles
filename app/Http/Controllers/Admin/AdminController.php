<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Repair;

class AdminController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $todaysSales = Sale::whereDate('sale_date', today())->sum('grand_total');
        $totalOrders = Sale::count();
        $pendingRepairs = Repair::whereNotIn('status', ['Ready', 'Delivered', 'Cancelled'])->count();

        $recentSales = Sale::with('customer')->latest()->take(10)->get();

        return view('admin.dashboard.index', compact(
            'totalProducts', 'todaysSales', 'totalOrders', 'pendingRepairs', 'recentSales'
        ));
    }
}
