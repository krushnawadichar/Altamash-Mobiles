<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\RepairJob;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $thisYearStart = Carbon::now()->startOfYear();

        // 1. Metric Cards
        $totalProducts = Product::count();
        $totalMobiles = Product::where('type', 'mobile')->count();
        $totalAccessories = Product::whereIn('type', ['accessory', 'spare_part'])->count();

        // Total Stock Value
        $accessoryStockValue = Product::whereIn('type', ['accessory', 'spare_part'])
            ->selectRaw('SUM(current_stock * purchase_price) as val')
            ->value('val') ?? 0;
        $mobileStockValue = ProductSerial::where('status', 'available')->sum('purchase_price');
        $totalStockValue = $accessoryStockValue + $mobileStockValue;

        // Today's Sales & Purchases
        $todaySales = Sale::whereDate('sale_date', $today)->where('status', 'completed')->sum('grand_total');
        $todayPurchases = Purchase::whereDate('purchase_date', $today)->sum('grand_total');

        // Today's Collection
        $todayCollection = SalePayment::whereDate('payment_date', $today)->sum('amount');

        // Today's Profit: Sales Revenue - COGS - Today's Expenses
        $todaySaleItemsCost = SaleItem::whereHas('sale', function ($q) use ($today) {
            $q->whereDate('sale_date', $today)->where('status', 'completed');
        })->selectRaw('SUM(unit_cost * quantity) as cost')->value('cost') ?? 0;
        $todayExpenses = Expense::whereDate('date', $today)->sum('amount');
        $todayProfit = $todaySales - $todaySaleItemsCost - $todayExpenses;

        // Pending Payments
        $pendingCustomerPayments = Sale::where('status', 'completed')->sum('due_amount');
        $pendingSupplierPayments = Purchase::sum('due_amount');

        // Repair Metrics
        $totalRepairs = RepairJob::count();
        $pendingRepairs = RepairJob::whereNotIn('status', ['Delivered', 'Cancelled'])->count();
        $completedRepairs = RepairJob::where('status', 'Repair Completed')->count();
        $readyRepairs = RepairJob::where('status', 'Ready for Delivery')->count();

        // Low stock products count
        $lowStockCount = Product::whereColumn('current_stock', '<=', 'min_stock')->count();

        // 2. Recent lists
        $recentSales = Sale::with('customer')
            ->latest()
            ->take(6)
            ->get();

        $recentPurchases = Purchase::with('supplier')
            ->latest()
            ->take(6)
            ->get();

        $lowStockProducts = Product::with(['brand', 'category'])
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->orderBy('current_stock', 'asc')
            ->take(6)
            ->get();

        $activeRepairs = RepairJob::with(['technician', 'brand'])
            ->whereNotIn('status', ['Delivered', 'Cancelled'])
            ->latest()
            ->take(6)
            ->get();

        // 3. Chart Data (Last 7 Days)
        $chartDates = [];
        $salesChartData = [];
        $purchaseChartData = [];
        $profitChartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::now()->subDays($i)->format('D, M d');

            $daySales = Sale::whereDate('sale_date', $date)->where('status', 'completed')->sum('grand_total');
            $dayPurchases = Purchase::whereDate('purchase_date', $date)->sum('grand_total');
            $dayCogs = SaleItem::whereHas('sale', function ($q) use ($date) {
                $q->whereDate('sale_date', $date)->where('status', 'completed');
            })->selectRaw('SUM(unit_cost * quantity) as cost')->value('cost') ?? 0;
            $dayExp = Expense::whereDate('date', $date)->sum('amount');
            $dayProfit = $daySales - $dayCogs - $dayExp;

            $salesChartData[] = (float) $daySales;
            $purchaseChartData[] = (float) $dayPurchases;
            $profitChartData[] = (float) $dayProfit;
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalMobiles',
            'totalAccessories',
            'totalStockValue',
            'todaySales',
            'todayPurchases',
            'todayProfit',
            'todayCollection',
            'pendingCustomerPayments',
            'pendingSupplierPayments',
            'totalRepairs',
            'pendingRepairs',
            'completedRepairs',
            'readyRepairs',
            'lowStockCount',
            'recentSales',
            'recentPurchases',
            'lowStockProducts',
            'activeRepairs',
            'chartDates',
            'salesChartData',
            'purchaseChartData',
            'profitChartData'
        ));
    }
}
