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
use App\Models\PurchaseDetail;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get Sales Report
     */
    public function getSalesReport($filters = [])
    {
        $query = Sale::with(['customer', 'details.sellable']);

        // Apply filters
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        // Calculate summary
        $summary = [
            'total_sales' => $sales->count(),
            'total_amount' => $sales->sum('total_amount'),
            'total_profit' => $sales->sum('total_amount') - $sales->sum('subtotal'),
            'total_discount' => $sales->sum('discount'),
            'total_gst' => $sales->sum('gst_amount'),
            'total_paid' => $sales->sum('paid_amount'),
            'total_due' => $sales->sum('due_amount'),
            'avg_sale_value' => $sales->count() > 0 ? $sales->avg('total_amount') : 0,
            'paid_count' => $sales->where('payment_status', 'paid')->count(),
            'partial_count' => $sales->where('payment_status', 'partial')->count(),
            'pending_count' => $sales->where('payment_status', 'pending')->count(),
        ];

        return [
            'sales' => $sales,
            'summary' => $summary,
            'filters' => $filters,
        ];
    }

    /**
     * Get Purchase Report
     */
    public function getPurchaseReport($filters = [])
    {
        $query = Purchase::with(['supplier', 'details.purchasable']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('purchase_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        $purchases = $query->orderBy('purchase_date', 'desc')->get();

        $summary = [
            'total_purchases' => $purchases->count(),
            'total_amount' => $purchases->sum('total_amount'),
            'total_discount' => $purchases->sum('discount'),
            'total_gst' => $purchases->sum('gst_amount'),
            'total_paid' => $purchases->sum('paid_amount'),
            'total_due' => $purchases->sum('due_amount'),
            'avg_purchase_value' => $purchases->count() > 0 ? $purchases->avg('total_amount') : 0,
            'paid_count' => $purchases->where('payment_status', 'paid')->count(),
            'partial_count' => $purchases->where('payment_status', 'partial')->count(),
            'pending_count' => $purchases->where('payment_status', 'pending')->count(),
        ];

        return [
            'purchases' => $purchases,
            'summary' => $summary,
            'filters' => $filters,
        ];
    }

    /**
     * Get Profit Report
     */
    public function getProfitReport($filters = [])
    {
        $query = Sale::with(['details.sellable']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['product_id'])) {
            $query->whereHas('details', function($q) use ($filters) {
                $q->where('sellable_id', $filters['product_id'])
                  ->where('sellable_type', 'App\Models\Product');
            });
        }

        $sales = $query->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalCost = $sales->sum('subtotal');
        $totalProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Product-wise profit
        $productProfits = DB::table('sale_details')
            ->join('products', 'sale_details.sellable_id', '=', 'products.id')
            ->where('sale_details.sellable_type', 'App\Models\Product')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_details.quantity) as total_sold'),
                DB::raw('SUM(sale_details.total) as total_revenue'),
                DB::raw('SUM(sale_details.profit) as total_profit'),
                DB::raw('(SUM(sale_details.profit) / SUM(sale_details.total)) * 100 as profit_margin')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_profit', 'desc');

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $productProfits->whereBetween('sale_details.created_at', [$filters['start_date'], $filters['end_date']]);
        }

        $productProfits = $productProfits->get();

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_cost' => $totalCost,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'total_sales' => $sales->count(),
            'avg_profit_per_sale' => $sales->count() > 0 ? $totalProfit / $sales->count() : 0,
            'top_product' => $productProfits->first() ? $productProfits->first()->name : null,
        ];

        return [
            'product_profits' => $productProfits,
            'summary' => $summary,
            'filters' => $filters,
            'sales' => $sales,
        ];
    }

    /**
     * Get Expense Report
     */
    public function getExpenseReport($filters = [])
    {
        $query = Expense::with('expenseCategory');

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('expense_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['category_id'])) {
            $query->where('expense_category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        $summary = [
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->where('status', 'paid')->sum('amount'),
            'total_pending' => $expenses->where('status', 'pending')->sum('amount'),
            'total_cancelled' => $expenses->where('status', 'cancelled')->sum('amount'),
            'avg_expense' => $expenses->count() > 0 ? $expenses->avg('amount') : 0,
            'paid_count' => $expenses->where('status', 'paid')->count(),
            'pending_count' => $expenses->where('status', 'pending')->count(),
            'cancelled_count' => $expenses->where('status', 'cancelled')->count(),
        ];

        // Category-wise expenses
        $categoryExpenses = Expense::join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select(
                'expense_categories.id',
                'expense_categories.name',
                DB::raw('SUM(expenses.amount) as total'),
                DB::raw('COUNT(expenses.id) as count')
            )
            ->where('expenses.status', 'paid');

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $categoryExpenses->whereBetween('expenses.expense_date', [$filters['start_date'], $filters['end_date']]);
        }

        $categoryExpenses = $categoryExpenses->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'expenses' => $expenses,
            'summary' => $summary,
            'category_expenses' => $categoryExpenses,
            'filters' => $filters,
        ];
    }

    /**
     * Get Repair Report
     */
    public function getRepairReport($filters = [])
    {
        $query = Repair::with(['customer', 'repairStatus']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('receive_date', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['status_id'])) {
            $query->where('repair_status_id', $filters['status_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        $repairs = $query->orderBy('receive_date', 'desc')->get();

        $summary = [
            'total_repairs' => $repairs->count(),
            'total_estimated_cost' => $repairs->sum('estimated_cost'),
            'total_advance_paid' => $repairs->sum('advance_paid'),
            'total_remaining' => $repairs->sum('remaining_amount'),
            'avg_cost' => $repairs->count() > 0 ? $repairs->avg('estimated_cost') : 0,
            'completed_count' => $repairs->where('repair_status.name', 'Completed')->count(),
            'pending_count' => $repairs->where('repair_status.name', 'Pending')->count(),
            'cancelled_count' => $repairs->where('repair_status.name', 'Cancelled')->count(),
            'paid_count' => $repairs->where('payment_status', 'paid')->count(),
            'partial_count' => $repairs->where('payment_status', 'partial')->count(),
            'pending_payment_count' => $repairs->where('payment_status', 'pending')->count(),
        ];

        return [
            'repairs' => $repairs,
            'summary' => $summary,
            'filters' => $filters,
        ];
    }

    /**
     * Get Inventory Report
     */
    public function getInventoryReport($filters = [])
    {
        // Product Inventory
        $products = Product::with(['category', 'brand', 'supplier'])
            ->where('is_active', true);

        if (isset($filters['category_id'])) {
            $products->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $products->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $products->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($filters['stock_status'] === 'out') {
                $products->where('current_stock', '<=', 0);
            } elseif ($filters['stock_status'] === 'in') {
                $products->where('current_stock', '>', 0);
            }
        }

        $products = $products->get();

        // Accessory Inventory
        $accessories = \App\Models\Accessory::with('supplier')
            ->where('is_active', true);

        if (isset($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $accessories->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($filters['stock_status'] === 'out') {
                $accessories->where('current_stock', '<=', 0);
            } elseif ($filters['stock_status'] === 'in') {
                $accessories->where('current_stock', '>', 0);
            }
        }

        $accessories = $accessories->get();

        $summary = [
            'total_products' => $products->count(),
            'total_accessories' => $accessories->count(),
            'total_items' => $products->count() + $accessories->count(),
            'product_stock_value' => $products->sum('current_stock * purchase_price'),
            'accessory_stock_value' => $accessories->sum('current_stock * purchase_price'),
            'total_stock_value' => $products->sum('current_stock * purchase_price') + $accessories->sum('current_stock * purchase_price'),
            'low_stock_products' => $products->where('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock_products' => $products->where('current_stock', '<=', 0)->count(),
            'low_stock_accessories' => $accessories->where('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock_accessories' => $accessories->where('current_stock', '<=', 0)->count(),
        ];

        return [
            'products' => $products,
            'accessories' => $accessories,
            'summary' => $summary,
            'filters' => $filters,
        ];
    }

    /**
     * Get Customer Report
     */
    public function getCustomerReport($filters = [])
    {
        $query = Customer::with(['sales']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereHas('sales', function($q) use ($filters) {
                $q->whereBetween('sale_date', [$filters['start_date'], $filters['end_date']]);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $customers = $query->get();

        $summary = [
            'total_customers' => $customers->count(),
            'active_customers' => $customers->where('is_active', true)->count(),
            'inactive_customers' => $customers->where('is_active', false)->count(),
            'total_balance' => $customers->sum('current_balance'),
            'customers_with_balance' => $customers->where('current_balance', '>', 0)->count(),
            'total_purchases' => $customers->sum('total_purchases'),
            'total_purchase_amount' => $customers->sum('total_purchase_amount'),
        ];

        // Top customers by spending
        $topCustomers = $customers->sortByDesc(function($customer) {
            return $customer->sales->sum('total_amount');
        })->take(10)->values();

        return [
            'customers' => $customers,
            'summary' => $summary,
            'top_customers' => $topCustomers,
            'filters' => $filters,
        ];
    }

    /**
     * Get Supplier Report
     */
    public function getSupplierReport($filters = [])
    {
        $query = Supplier::with(['purchases']);

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereHas('purchases', function($q) use ($filters) {
                $q->whereBetween('purchase_date', [$filters['start_date'], $filters['end_date']]);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $suppliers = $query->get();

        $summary = [
            'total_suppliers' => $suppliers->count(),
            'active_suppliers' => $suppliers->where('is_active', true)->count(),
            'inactive_suppliers' => $suppliers->where('is_active', false)->count(),
            'total_balance' => $suppliers->sum('current_balance'),
            'suppliers_with_balance' => $suppliers->where('current_balance', '>', 0)->count(),
        ];

        // Top suppliers by purchase amount
        $topSuppliers = $suppliers->sortByDesc(function($supplier) {
            return $supplier->purchases->sum('total_amount');
        })->take(10)->values();

        return [
            'suppliers' => $suppliers,
            'summary' => $summary,
            'top_suppliers' => $topSuppliers,
            'filters' => $filters,
        ];
    }

    /**
     * Get Daily Report
     */
    public function getDailyReport($date = null)
    {
        $date = $date ?? date('Y-m-d');

        $sales = Sale::whereDate('sale_date', $date)->get();
        $purchases = Purchase::whereDate('purchase_date', $date)->get();
        $expenses = Expense::whereDate('expense_date', $date)->get();
        $repairs = Repair::whereDate('receive_date', $date)->get();

        return [
            'date' => $date,
            'sales' => [
                'count' => $sales->count(),
                'total' => $sales->sum('total_amount'),
                'profit' => $sales->sum('total_amount') - $sales->sum('subtotal'),
            ],
            'purchases' => [
                'count' => $purchases->count(),
                'total' => $purchases->sum('total_amount'),
            ],
            'expenses' => [
                'count' => $expenses->count(),
                'total' => $expenses->where('status', 'paid')->sum('amount'),
            ],
            'repairs' => [
                'count' => $repairs->count(),
                'total' => $repairs->sum('estimated_cost'),
                'pending' => $repairs->where('repair_status.name', 'Pending')->count(),
                'completed' => $repairs->where('repair_status.name', 'Completed')->count(),
            ],
            'net_profit' => $sales->sum('total_amount') - $sales->sum('subtotal') - $expenses->where('status', 'paid')->sum('amount'),
        ];
    }

    /**
     * Get Weekly Report
     */
    public function getWeeklyReport($startDate = null)
    {
        $startDate = $startDate ?? Carbon::now()->startOfWeek()->format('Y-m-d');
        $endDate = Carbon::parse($startDate)->endOfWeek()->format('Y-m-d');

        $report = $this->getReportByDateRange($startDate, $endDate);
        $report['period'] = 'weekly';
        $report['start_date'] = $startDate;
        $report['end_date'] = $endDate;
        $report['week_number'] = Carbon::parse($startDate)->weekOfYear;

        return $report;
    }

    /**
     * Get Monthly Report
     */
    public function getMonthlyReport($month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        $report = $this->getReportByDateRange($startDate, $endDate);
        $report['period'] = 'monthly';
        $report['month'] = $month;
        $report['year'] = $year;
        $report['month_name'] = Carbon::createFromDate($year, $month, 1)->format('F');
        $report['start_date'] = $startDate;
        $report['end_date'] = $endDate;

        return $report;
    }

    /**
     * Get Yearly Report
     */
    public function getYearlyReport($year = null)
    {
        $year = $year ?? date('Y');

        $startDate = Carbon::createFromDate($year, 1, 1)->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, 12, 31)->format('Y-m-d');

        $report = $this->getReportByDateRange($startDate, $endDate);
        $report['period'] = 'yearly';
        $report['year'] = $year;
        $report['start_date'] = $startDate;
        $report['end_date'] = $endDate;

        return $report;
    }

    /**
     * Get Report by Custom Date Range
     */
    public function getReportByDateRange($startDate, $endDate)
    {
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])->get();
        $purchases = Purchase::whereBetween('purchase_date', [$startDate, $endDate])->get();
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();
        $repairs = Repair::whereBetween('receive_date', [$startDate, $endDate])->get();

        return [
            'sales' => [
                'count' => $sales->count(),
                'total' => $sales->sum('total_amount'),
                'profit' => $sales->sum('total_amount') - $sales->sum('subtotal'),
                'discount' => $sales->sum('discount'),
                'gst' => $sales->sum('gst_amount'),
                'paid' => $sales->sum('paid_amount'),
                'due' => $sales->sum('due_amount'),
            ],
            'purchases' => [
                'count' => $purchases->count(),
                'total' => $purchases->sum('total_amount'),
                'discount' => $purchases->sum('discount'),
                'gst' => $purchases->sum('gst_amount'),
                'paid' => $purchases->sum('paid_amount'),
                'due' => $purchases->sum('due_amount'),
            ],
            'expenses' => [
                'count' => $expenses->count(),
                'total' => $expenses->sum('amount'),
                'paid' => $expenses->where('status', 'paid')->sum('amount'),
                'pending' => $expenses->where('status', 'pending')->sum('amount'),
                'cancelled' => $expenses->where('status', 'cancelled')->sum('amount'),
            ],
            'repairs' => [
                'count' => $repairs->count(),
                'estimated_total' => $repairs->sum('estimated_cost'),
                'advance_total' => $repairs->sum('advance_paid'),
                'remaining_total' => $repairs->sum('remaining_amount'),
                'completed' => $repairs->where('repair_status.name', 'Completed')->count(),
                'pending' => $repairs->where('repair_status.name', 'Pending')->count(),
            ],
            'summary' => [
                'total_sales' => $sales->sum('total_amount'),
                'total_purchases' => $purchases->sum('total_amount'),
                'total_expenses' => $expenses->where('status', 'paid')->sum('amount'),
                'net_profit' => ($sales->sum('total_amount') - $sales->sum('subtotal')) - $expenses->where('status', 'paid')->sum('amount'),
                'cash_in_hand' => $sales->sum('paid_amount') - $purchases->sum('paid_amount') - $expenses->where('status', 'paid')->sum('amount'),
            ],
        ];
    }

    /**
     * Get Report Data for Export
     */
    public function getReportData($type, $filters = [])
    {
        switch ($type) {
            case 'sales':
                return $this->getSalesReport($filters);
            case 'purchases':
                return $this->getPurchaseReport($filters);
            case 'profit':
                return $this->getProfitReport($filters);
            case 'expenses':
                return $this->getExpenseReport($filters);
            case 'repairs':
                return $this->getRepairReport($filters);
            case 'inventory':
                return $this->getInventoryReport($filters);
            case 'customers':
                return $this->getCustomerReport($filters);
            case 'suppliers':
                return $this->getSupplierReport($filters);
            default:
                return [
                    'data' => [],
                    'summary' => [],
                    'filters' => $filters,
                ];
        }
    }

    /**
     * Get Business Analytics Data
     */
    public function getBusinessAnalytics()
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $weekStart = Carbon::now()->startOfWeek()->format('Y-m-d');
        $monthStart = Carbon::now()->startOfMonth()->format('Y-m-d');
        $yearStart = Carbon::now()->startOfYear()->format('Y-m-d');

        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $yesterdaySales = Sale::whereDate('sale_date', $yesterday)->sum('total_amount');
        $weekSales = Sale::whereDate('sale_date', '>=', $weekStart)->sum('total_amount');
        $monthSales = Sale::whereDate('sale_date', '>=', $monthStart)->sum('total_amount');
        $yearSales = Sale::whereDate('sale_date', '>=', $yearStart)->sum('total_amount');

        $todayProfit = Sale::whereDate('sale_date', $today)->sum('total_amount') - Sale::whereDate('sale_date', $today)->sum('subtotal');
        $monthProfit = Sale::whereDate('sale_date', '>=', $monthStart)->sum('total_amount') - Sale::whereDate('sale_date', '>=', $monthStart)->sum('subtotal');

        // Best Selling Products
        $bestSellingProducts = DB::table('sale_details')
            ->join('products', 'sale_details.sellable_id', '=', 'products.id')
            ->where('sale_details.sellable_type', 'App\Models\Product')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                DB::raw('SUM(sale_details.quantity) as total_sold'),
                DB::raw('SUM(sale_details.total) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Best Selling Accessories
        $bestSellingAccessories = DB::table('sale_details')
            ->join('accessories', 'sale_details.sellable_id', '=', 'accessories.id')
            ->where('sale_details.sellable_type', 'App\Models\Accessory')
            ->select(
                'accessories.id',
                'accessories.name',
                'accessories.sku',
                DB::raw('SUM(sale_details.quantity) as total_sold'),
                DB::raw('SUM(sale_details.total) as total_revenue')
            )
            ->groupBy('accessories.id', 'accessories.name', 'accessories.sku')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Top Customers
        $topCustomers = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'customers.id',
                'customers.name',
                'customers.phone',
                'customers.email',
                DB::raw('COUNT(sales.id) as total_orders'),
                DB::raw('SUM(sales.total_amount) as total_spent')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.phone', 'customers.email')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // Top Suppliers
        $topSuppliers = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'suppliers.id',
                'suppliers.name',
                'suppliers.phone',
                'suppliers.email',
                DB::raw('COUNT(purchases.id) as total_orders'),
                DB::raw('SUM(purchases.total_amount) as total_spent')
            )
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.phone', 'suppliers.email')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        return [
            'today_sales' => $todaySales,
            'yesterday_sales' => $yesterdaySales,
            'weekly_sales' => $weekSales,
            'monthly_sales' => $monthSales,
            'yearly_sales' => $yearSales,
            'today_profit' => $todayProfit,
            'monthly_profit' => $monthProfit,
            'best_selling_products' => $bestSellingProducts,
            'best_selling_accessories' => $bestSellingAccessories,
            'top_customers' => $topCustomers,
            'top_suppliers' => $topSuppliers,
            'low_stock_products' => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            'out_of_stock_products' => Product::where('current_stock', '<=', 0)->count(),
            'total_stock_value' => Product::sum('current_stock * purchase_price') + \App\Models\Accessory::sum('current_stock * purchase_price'),
            'total_sales_value' => Sale::sum('total_amount'),
            'total_purchase_value' => Purchase::sum('total_amount'),
            'profit_margin' => Sale::sum('total_amount') > 0 ? ((Sale::sum('total_amount') - Sale::sum('subtotal')) / Sale::sum('total_amount')) * 100 : 0,
        ];
    }
}