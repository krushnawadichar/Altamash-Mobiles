<?php

namespace App\Repositories;

use App\Models\Supplier;

class SupplierRepository extends BaseRepository
{
    public function __construct(Supplier $supplier)
    {
        parent::__construct($supplier);
    }

    public function getWithPurchases()
    {
        return $this->model->with('purchases')->get();
    }

    public function getActiveWithPurchases()
    {
        return $this->model->where('is_active', true)->with('purchases')->get();
    }

    public function getSuppliersWithBalance()
    {
        return $this->model->where('current_balance', '>', 0)->get();
    }

    public function getSuppliersWithDueBalance()
    {
        return $this->model->where('current_balance', '>', 0)
                          ->orderBy('current_balance', 'desc')
                          ->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhere('phone', 'like', "%{$query}%")
                          ->orWhere('company_name', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}