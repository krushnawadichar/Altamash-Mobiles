<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository extends BaseRepository
{
    public function __construct(Sale $sale)
    {
        parent::__construct($sale);
    }

    public function getByInvoiceNumber($invoiceNumber)
    {
        return $this->model->where('invoice_number', $invoiceNumber)->first();
    }

    public function getWithCustomerAndDetails()
    {
        return $this->model->with(['customer', 'details.sellable'])->get();
    }

    public function getByCustomer($customerId)
    {
        return $this->model->where('customer_id', $customerId)
                          ->with(['customer', 'details.sellable'])
                          ->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('sale_date', [$startDate, $endDate])
                          ->with(['customer', 'details.sellable'])
                          ->get();
    }

    public function getByStatus($status)
    {
        return $this->model->where('payment_status', $status)
                          ->with(['customer', 'details.sellable'])
                          ->get();
    }

    public function getPendingPayments()
    {
        return $this->model->where('payment_status', '!=', 'paid')
                          ->where('due_amount', '>', 0)
                          ->with('customer')
                          ->get();
    }

    public function getTotalSales($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        }
        
        return $query->sum('total_amount');
    }

    public function getTotalProfit($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        }
        
        return $query->sum('total_amount') - $query->sum('subtotal');
    }

    public function getMonthlySales($year = null)
    {
        $year = $year ?? date('Y');
        return $this->model->selectRaw('MONTH(sale_date) as month, SUM(total_amount) as total')
                          ->whereYear('sale_date', $year)
                          ->groupBy('month')
                          ->orderBy('month')
                          ->pluck('total', 'month');
    }

    public function getMonthlyProfit($year = null)
    {
        $year = $year ?? date('Y');
        return $this->model->selectRaw('MONTH(sale_date) as month, SUM(total_amount) - SUM(subtotal) as profit')
                          ->whereYear('sale_date', $year)
                          ->groupBy('month')
                          ->orderBy('month')
                          ->pluck('profit', 'month');
    }

    public function search($query)
    {
        return $this->model->where('invoice_number', 'like', "%{$query}%")
                          ->orWhereHas('customer', function($q) use ($query) {
                              $q->where('name', 'like', "%{$query}%")
                                ->orWhere('phone', 'like', "%{$query}%");
                          })
                          ->with(['customer', 'details.sellable'])
                          ->paginate(15);
    }

    public function getRecentSales($limit = 10)
    {
        return $this->model->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->with(['customer', 'details.sellable'])
                          ->get();
    }

    public function getLastSale()
    {
        return $this->model->orderBy('id', 'desc')->first();
    }

    public function getTopSellingProducts($limit = 10, $startDate = null, $endDate = null)
    {
        $query = $this->model->join('sale_details', 'sales.id', '=', 'sale_details.sale_id')
                            ->join('products', 'sale_details.sellable_id', '=', 'products.id')
                            ->where('sale_details.sellable_type', 'App\Models\Product')
                            ->select(
                                'products.id',
                                'products.name',
                                'products.sku',
                                'products.selling_price',
                                'products.purchase_price',
                                \DB::raw('SUM(sale_details.quantity) as total_sold'),
                                \DB::raw('SUM(sale_details.total) as total_revenue'),
                                \DB::raw('SUM(sale_details.profit) as total_profit')
                            )
                            ->groupBy('products.id', 'products.name', 'products.sku', 'products.selling_price', 'products.purchase_price')
                            ->orderBy('total_sold', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('sales.sale_date', [$startDate, $endDate]);
        }

        return $query->limit($limit)->get();
    }

    public function getTopCustomers($limit = 10, $startDate = null, $endDate = null)
    {
        $query = $this->model->join('customers', 'sales.customer_id', '=', 'customers.id')
                            ->select(
                                'customers.id',
                                'customers.name',
                                'customers.phone',
                                'customers.email',
                                \DB::raw('COUNT(sales.id) as total_orders'),
                                \DB::raw('SUM(sales.total_amount) as total_spent')
                            )
                            ->groupBy('customers.id', 'customers.name', 'customers.phone', 'customers.email')
                            ->orderBy('total_spent', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('sales.sale_date', [$startDate, $endDate]);
        }

        return $query->limit($limit)->get();
    }

    public function getDailySales($date = null)
    {
        $date = $date ?? date('Y-m-d');
        return $this->model->whereDate('sale_date', $date)
                          ->sum('total_amount');
    }
}