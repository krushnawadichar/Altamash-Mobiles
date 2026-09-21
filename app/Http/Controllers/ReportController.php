<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\RepairJob;
use App\Models\RepairPartUsed;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\Technician;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        $query = Sale::with(['customer', 'items.product'])->where('status', 'completed');

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        $query->whereDate('sale_date', '>=', $fromDate)->whereDate('sale_date', '<=', $toDate);

        $sales = (clone $query)->latest('sale_date')->paginate(20)->withQueryString();

        $totals = (clone $query)->reorder()->selectRaw('
            COUNT(id) as count,
            SUM(subtotal) as subtotal,
            SUM(discount_amount) as discount,
            SUM(tax_amount) as tax,
            SUM(grand_total) as total,
            SUM(paid_amount) as paid,
            SUM(due_amount) as due
        ')->first();

        $totalRevenue = $totals->total ?? 0;
        $totalPaid = $totals->paid ?? 0;
        $totalDue = $totals->due ?? 0;
        $totalInvoices = $totals->count ?? 0;

        // Profit
        $filteredSaleIds = (clone $query)->reorder()->pluck('id');
        $totalCost = SaleItem::whereIn('sale_id', $filteredSaleIds)
            ->selectRaw('SUM(unit_cost * quantity) as total_cost')
            ->value('total_cost') ?? 0;

        $totalProfit = $totalRevenue - $totalCost;
        $customers = Customer::all();

        return view('admin.reports.sales', compact(
            'sales',
            'totals',
            'totalRevenue',
            'totalPaid',
            'totalDue',
            'totalInvoices',
            'totalProfit',
            'fromDate',
            'toDate',
            'customers'
        ));
    }

    public function purchases(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        $query = Purchase::with(['supplier']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $query->whereDate('purchase_date', '>=', $fromDate)->whereDate('purchase_date', '<=', $toDate);

        $purchases = (clone $query)->latest('purchase_date')->paginate(20)->withQueryString();

        $totals = (clone $query)->reorder()->selectRaw('
            COUNT(id) as count,
            SUM(subtotal) as subtotal,
            SUM(tax_amount) as tax,
            SUM(discount_amount) as discount,
            SUM(grand_total) as total,
            SUM(paid_amount) as paid,
            SUM(due_amount) as due
        ')->first();

        $totalPurchases = $totals->total ?? 0;
        $totalPaid = $totals->paid ?? 0;
        $totalDue = $totals->due ?? 0;
        $totalOrders = $totals->count ?? 0;

        $suppliers = Supplier::all();

        return view('admin.reports.purchases', compact(
            'purchases',
            'totals',
            'totalPurchases',
            'totalPaid',
            'totalDue',
            'totalOrders',
            'fromDate',
            'toDate',
            'suppliers'
        ));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(['brand', 'category'])->get();

        $totalItems = $products->count();
        $totalStockQty = $products->sum('current_stock');

        $stockValueCost = $products->sum(fn($p) => $p->purchase_price * $p->current_stock);
        $stockValueSelling = $products->sum(fn($p) => $p->selling_price * $p->current_stock);
        $potentialProfit = $stockValueSelling - $stockValueCost;

        $lowStockProducts = Product::whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0)->get();
        $outOfStockProducts = Product::where('current_stock', '<=', 0)->get();

        return view('admin.reports.inventory', compact(
            'products',
            'totalItems',
            'totalStockQty',
            'stockValueCost',
            'stockValueSelling',
            'potentialProfit',
            'lowStockProducts',
            'outOfStockProducts'
        ));
    }

    public function mobiles(Request $request)
    {
        $mobileProducts = Product::with(['brand', 'category', 'availableSerials'])
            ->where('type', 'mobile')
            ->get();

        $availableImeisCount = ProductSerial::where('status', 'available')->count();
        $soldImeisCount = ProductSerial::where('status', 'sold')->count();
        $mobileStockValue = ProductSerial::where('status', 'available')->sum('purchase_price');

        return view('admin.reports.mobiles', compact(
            'mobileProducts',
            'availableImeisCount',
            'soldImeisCount',
            'mobileStockValue'
        ));
    }

    public function repairs(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        $query = RepairJob::with(['technician', 'brand', 'customer']);

        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }

        $query->whereDate('received_date', '>=', $fromDate)->whereDate('received_date', '<=', $toDate);

        $repairs = (clone $query)->latest('received_date')->paginate(20)->withQueryString();

        $totalJobs = (clone $query)->count();
        $completedJobs = (clone $query)->where('status', 'Delivered')->count();
        $totalRevenue = (clone $query)->sum('final_cost');
        
        $repairIds = (clone $query)->pluck('id');
        $partsCost = RepairPartUsed::whereIn('repair_job_id', $repairIds)->sum('subtotal');

        $technicians = Technician::all();

        return view('admin.reports.repairs', compact(
            'repairs',
            'totalJobs',
            'completedJobs',
            'totalRevenue',
            'partsCost',
            'fromDate',
            'toDate',
            'technicians'
        ));
    }

    public function profitLoss(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', Carbon::now()->toDateString());

        // Sales Revenue
        $salesQuery = Sale::where('status', 'completed')
            ->whereDate('sale_date', '>=', $fromDate)
            ->whereDate('sale_date', '<=', $toDate);
        
        $salesRevenue = (clone $salesQuery)->sum('grand_total');

        $saleIds = (clone $salesQuery)->pluck('id');
        $salesCost = SaleItem::whereIn('sale_id', $saleIds)
            ->selectRaw('SUM(unit_cost * quantity) as cogs')
            ->value('cogs') ?? 0;

        // Repair Revenue
        $repairQuery = RepairJob::where('status', 'Delivered')
            ->whereDate('received_date', '>=', $fromDate)
            ->whereDate('received_date', '<=', $toDate);
        $repairRevenue = (clone $repairQuery)->sum('final_cost');

        $repairIds = (clone $repairQuery)->pluck('id');
        $repairPartsCost = RepairPartUsed::whereIn('repair_job_id', $repairIds)->sum('subtotal');

        $totalCogs = $salesCost + $repairPartsCost;
        $grossProfit = ($salesRevenue + $repairRevenue) - $totalCogs;

        // Expenses
        $expensesQuery = Expense::whereDate('date', '>=', $fromDate)->whereDate('date', '<=', $toDate);
        $totalExpenses = (clone $expensesQuery)->sum('amount');
        
        $expensesByCategory = Expense::with('category')
            ->whereIn('id', (clone $expensesQuery)->pluck('id'))
            ->get()
            ->groupBy('category.name')
            ->map(fn($group) => $group->sum('amount'));

        $netProfit = $grossProfit - $totalExpenses;

        return view('admin.reports.profit_loss', compact(
            'fromDate',
            'toDate',
            'salesRevenue',
            'repairRevenue',
            'salesCost',
            'repairPartsCost',
            'totalCogs',
            'grossProfit',
            'expensesByCategory',
            'totalExpenses',
            'netProfit'
        ));
    }
}
