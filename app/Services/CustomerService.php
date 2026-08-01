<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers()
    {
        return $this->customerRepository->all();
    }

    public function getActiveCustomers()
    {
        return $this->customerRepository->getActive();
    }

    public function getCustomerById($id)
    {
        return $this->customerRepository->find($id);
    }

    public function createCustomer(array $data)
    {
        $data['created_by'] = auth()->id();
        $data['current_balance'] = $data['opening_balance'] ?? 0;
        return $this->customerRepository->create($data);
    }

    public function updateCustomer($id, array $data)
    {
        $customer = $this->customerRepository->find($id);
        return $this->customerRepository->update($customer, $data);
    }

    public function deleteCustomer($id)
    {
        $customer = $this->customerRepository->find($id);
        return $this->customerRepository->delete($customer);
    }

    public function updateBalance($id, $amount, $type = 'add')
    {
        $customer = $this->customerRepository->find($id);
        
        if ($type === 'add') {
            $customer->current_balance += $amount;
        } else {
            $customer->current_balance -= $amount;
        }
        
        return $customer->save();
    }

    public function incrementPurchaseCount($id)
    {
        $customer = $this->customerRepository->find($id);
        $customer->total_purchases += 1;
        return $customer->save();
    }

    public function toggleStatus($id)
    {
        $customer = $this->customerRepository->find($id);
        $customer->is_active = !$customer->is_active;
        return $customer->save();
    }
}