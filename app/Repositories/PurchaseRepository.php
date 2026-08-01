<?php

namespace App\Repositories;

use App\Models\Purchase;

class PurchaseRepository extends BaseRepository
{
    public function __construct(Purchase $purchase)
    {
        parent::__construct($purchase);
    }

    public function getByInvoiceNumber($invoiceNumber)
    {
        return $this->model->where('invoice_number', $invoiceNumber)->first();
    }

    public function getWithSupplierAndDetails()
    {
        return $this->model->with(['supplier', 'details.purchasable'])->get();
    }

    public function getBySupplier($supplierId)
    {
        return $this->model->where('supplier_id', $supplierId)
                          ->with(['supplier', 'details.purchasable'])
                          ->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('purchase_date', [$startDate, $endDate])
                          ->with(['supplier', 'details.purchasable'])
                          ->get();
    }

    public function getByStatus($status)
    {
        return $this->model->where('payment_status', $status)
                          ->with(['supplier', 'details.purchasable'])
                          ->get();
    }

    public function getPendingPayments()
    {
        return $this->model->where('payment_status', '!=', 'paid')
                          ->where('due_amount', '>', 0)
                          ->with('supplier')
                          ->get();
    }

    public function getTotalPurchases($startDate = null, $endDate = null)
    {
        $query = $this->model->query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        }
        
        return $query->sum('total_amount');
    }

    public function getMonthlyPurchases($year = null)
    {
        $year = $year ?? date('Y');
        return $this->model->selectRaw('MONTH(purchase_date) as month, SUM(total_amount) as total')
                          ->whereYear('purchase_date', $year)
                          ->groupBy('month')
                          ->orderBy('month')
                          ->pluck('total', 'month');
    }

    public function search($query)
    {
        return $this->model->where('invoice_number', 'like', "%{$query}%")
                          ->orWhereHas('supplier', function($q) use ($query) {
                              $q->where('name', 'like', "%{$query}%")
                                ->orWhere('phone', 'like', "%{$query}%");
                          })
                          ->with(['supplier', 'details.purchasable'])
                          ->paginate(15);
    }

    public function getRecentPurchases($limit = 10)
    {
        return $this->model->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->with(['supplier', 'details.purchasable'])
                          ->get();
    }

    public function getLastPurchase()
    {
        return $this->model->orderBy('id', 'desc')->first();
    }
}