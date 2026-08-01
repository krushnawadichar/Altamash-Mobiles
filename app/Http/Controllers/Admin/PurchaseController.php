<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseRequest;
use App\Services\PurchaseService;
use App\Services\SupplierService;
use App\Services\ProductService;
use App\Services\AccessoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $purchaseService;
    protected $supplierService;
    protected $productService;
    protected $accessoryService;

    public function __construct(
        PurchaseService $purchaseService,
        SupplierService $supplierService,
        ProductService $productService,
        AccessoryService $accessoryService
    ) {
        $this->purchaseService = $purchaseService;
        $this->supplierService = $supplierService;
        $this->productService = $productService;
        $this->accessoryService = $accessoryService;
    }

    public function index()
    {
        $purchases = $this->purchaseService->getAllPurchases();
        return view('admin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = $this->supplierService->getActiveSuppliers();
        $products = $this->productService->getAllProducts();
        $accessories = $this->accessoryService->getAllAccessories();
        return view('admin.purchases.create', compact('suppliers', 'products', 'accessories'));
    }

    public function store(PurchaseRequest $request)
    {
        $this->purchaseService->createPurchase($request->validated());
        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase created successfully.');
    }

    public function show($id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function destroy($id)
    {
        $this->purchaseService->deletePurchase($id);
        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }

    public function print($id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        return view('admin.purchases.print', compact('purchase'));
    }

    public function pdf($id)
    {
        $purchase = $this->purchaseService->getPurchaseById($id);
        $pdf = Pdf::loadView('admin.purchases.pdf', compact('purchase'));
        return $pdf->download('purchase-' . $purchase->invoice_number . '.pdf');
    }
}