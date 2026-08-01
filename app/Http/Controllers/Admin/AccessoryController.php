<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccessoryRequest;
use App\Services\AccessoryService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class AccessoryController extends Controller
{
    protected $accessoryService;
    protected $supplierService;

    public function __construct(AccessoryService $accessoryService, SupplierService $supplierService)
    {
        $this->accessoryService = $accessoryService;
        $this->supplierService = $supplierService;
    }

    public function index()
    {
        $accessories = $this->accessoryService->getAllAccessories();
        return view('admin.accessories.index', compact('accessories'));
    }

    public function create()
    {
        $suppliers = $this->supplierService->getActiveSuppliers();
        return view('admin.accessories.create', compact('suppliers'));
    }

    public function store(AccessoryRequest $request)
    {
        $this->accessoryService->createAccessory($request->validated());
        return redirect()->route('admin.accessories.index')
            ->with('success', 'Accessory created successfully.');
    }

    public function edit($id)
    {
        $accessory = $this->accessoryService->getAccessoryById($id);
        $suppliers = $this->supplierService->getActiveSuppliers();
        return view('admin.accessories.edit', compact('accessory', 'suppliers'));
    }

    public function update(AccessoryRequest $request, $id)
    {
        $this->accessoryService->updateAccessory($id, $request->validated());
        return redirect()->route('admin.accessories.index')
            ->with('success', 'Accessory updated successfully.');
    }

    public function destroy($id)
    {
        $this->accessoryService->deleteAccessory($id);
        return redirect()->route('admin.accessories.index')
            ->with('success', 'Accessory deleted successfully.');
    }

    public function generateBarcode($id)
    {
        $accessory = $this->accessoryService->getAccessoryById($id);
        return view('admin.accessories.barcode', compact('accessory'));
    }
}