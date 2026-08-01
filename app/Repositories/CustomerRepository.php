<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository extends BaseRepository
{
    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
    }

    public function getWithSales()
    {
        return $this->model->with('sales')->get();
    }

    public function getActiveWithSales()
    {
        return $this->model->where('is_active', true)->with('sales')->get();
    }

    public function getCustomersWithBalance()
    {
        return $this->model->where('current_balance', '>', 0)->get();
    }

    public function getCustomersWithDueBalance()
    {
        return $this->model->where('current_balance', '>', 0)
                          ->orderBy('current_balance', 'desc')
                          ->get();
    }

    public function getTopCustomers($limit = 10)
    {
        return $this->model->orderBy('total_purchases', 'desc')
                          ->limit($limit)
                          ->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhere('phone', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getByPhone($phone)
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function incrementPurchases($id)
    {
        $customer = $this->find($id);
        if ($customer) {
            $customer->total_purchases += 1;
            $customer->save();
            return $customer;
        }
        return null;
    }

    public function updateTotalPurchaseAmount($id, $amount)
    {
        $customer = $this->find($id);
        if ($customer) {
            $customer->total_purchase_amount += $amount;
            $customer->save();
            return $customer;
        }
        return null;
    }
}