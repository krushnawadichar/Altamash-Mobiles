<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(CustomerRequest $request)
    {
        $this->customerService->createCustomer($request->validated());
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, $id)
    {
        $this->customerService->updateCustomer($id, $request->validated());
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->customerService->toggleStatus($id);
        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer status updated successfully.');
    }
}