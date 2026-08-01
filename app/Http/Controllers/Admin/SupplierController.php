<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        $suppliers = $this->supplierService->getAllSuppliers();
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        $this->supplierService->createSupplier($request->validated());
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = $this->supplierService->getSupplierById($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, $id)
    {
        $this->supplierService->updateSupplier($id, $request->validated());
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        $this->supplierService->deleteSupplier($id);
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->supplierService->toggleStatus($id);
        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier status updated successfully.');
    }
}