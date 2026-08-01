<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Repair;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SaleDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        return [
            'today_sales' => Sale::whereDate('sale_date', $today)->sum('total_amount') ?? 0,
            'today_profit' => Sale::whereDate('sale_date', $today)->sum('total_amount') - 
                             Sale::whereDate('sale_date', $today)->sum('subtotal') ?? 0,
            'today_purchases' => Purchase::whereDate('purchase_date', $today)->sum('total_amount') ?? 0,
            'today_expenses' => Expense::whereDate('expense_date', $today)->sum('amount') ?? 0,
            'today_repairs' => Repair::whereDate('receive_date', $today)->count(),
            'pending_repairs' => Repair::whereHas('repairStatus', function($q) {
                $q->whereIn('name', ['Pending', 'Checking', 'Waiting Parts', 'Repairing']);
            })->count(),
            'completed_repairs' => Repair::whereHas('repairStatus', function($q) {
                $q->where('name', 'Completed');
            })->count(),
            'total_products' => Product::count(),
            'low_stock' => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock' => Product::where('current_stock', '<=', 0)->count(),
            'total_customers' => Customer::count(),
            'total_suppliers' => Supplier::count(),
        ];
    }

    public function getSalesChartData()
    {
        $months = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $monthlySales = Sale::whereMonth('sale_date', $i)
                               ->whereYear('sale_date', date('Y'))
                               ->sum('total_amount') ?? 0;
            $data[] = (float) $monthlySales;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    public function getPurchaseChartData()
    {
        $months = [];
        $data = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));
            $monthlyPurchases = Purchase::whereMonth('purchase_date', $i)
                                      ->whereYear('purchase_date', date('Y'))
                                      ->sum('total_amount') ?? 0;
            $data[] = (float) $monthlyPurchases;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    public function getTopSellingProducts($limit = 5)
    {
        // Get top selling products from sale details
        $topProducts = DB::table('sale_details')
            ->join('products', 'sale_details.sellable_id', '=', 'products.id')
            ->where('sale_details.sellable_type', 'App\Models\Product')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_details.quantity) as total_sold'),
                DB::raw('SUM(sale_details.total) as total_revenue')
            )
            ->where('sale_details.deleted_at', null)
            ->where('products.deleted_at', null)
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();

        // If no data, return empty collection
        if ($topProducts->isEmpty()) {
            return collect([]);
        }

        return $topProducts;
    }

    public function getRecentSales($limit = 5)
    {
        return Sale::with('customer')
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get() ?? collect([]);
    }

    public function getRecentRepairs($limit = 5)
    {
        return Repair::with('repairStatus', 'customer')
                     ->orderBy('created_at', 'desc')
                     ->limit($limit)
                     ->get() ?? collect([]);
    }

    public function getRecentPurchases($limit = 5)
    {
        return Purchase::with('supplier')
                       ->orderBy('created_at', 'desc')
                       ->limit($limit)
                       ->get() ?? collect([]);
    }

    public function globalSearch($query)
    {
        $results = [];

        // Search Products
        $products = Product::where('name', 'like', "%{$query}%")
                          ->orWhere('sku', 'like', "%{$query}%")
                          ->orWhere('barcode', 'like', "%{$query}%")
                          ->orWhere('imei', 'like', "%{$query}%")
                          ->limit(5)
                          ->get();
        
        if ($products->count() > 0) {
            $results['products'] = $products;
        }

        // Search Customers
        $customers = Customer::where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('phone', 'like', "%{$query}%")
                            ->limit(5)
                            ->get();
        
        if ($customers->count() > 0) {
            $results['customers'] = $customers;
        }

        // Search Sales
        $sales = Sale::where('invoice_number', 'like', "%{$query}%")
                     ->limit(5)
                     ->get();
        
        if ($sales->count() > 0) {
            $results['sales'] = $sales;
        }

        // Search Repairs
        $repairs = Repair::where('repair_number', 'like', "%{$query}%")
                        ->orWhere('customer_name', 'like', "%{$query}%")
                        ->orWhere('customer_mobile', 'like', "%{$query}%")
                        ->limit(5)
                        ->get();
        
        if ($repairs->count() > 0) {
            $results['repairs'] = $repairs;
        }

        return $results;
    }
}