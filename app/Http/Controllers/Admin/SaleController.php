<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaleRequest;
use App\Services\SaleService;
use App\Services\CustomerService;
use App\Services\ProductService;
use App\Services\AccessoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $saleService;
    protected $customerService;
    protected $productService;
    protected $accessoryService;

    public function __construct(
        SaleService $saleService,
        CustomerService $customerService,
        ProductService $productService,
        AccessoryService $accessoryService
    ) {
        $this->saleService = $saleService;
        $this->customerService = $customerService;
        $this->productService = $productService;
        $this->accessoryService = $accessoryService;
    }

    public function index()
    {
        $sales = $this->saleService->getAllSales();
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = $this->customerService->getActiveCustomers();
        $products = $this->productService->getAllProducts();
        $accessories = $this->accessoryService->getAllAccessories();
        return view('admin.sales.create', compact('customers', 'products', 'accessories'));
    }

    public function store(SaleRequest $request)
    {
        $this->saleService->createSale($request->validated());
        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale created successfully.');
    }

    public function show($id)
    {
        $sale = $this->saleService->getSaleById($id);
        return view('admin.sales.show', compact('sale'));
    }

    public function destroy($id)
    {
        $this->saleService->deleteSale($id);
        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    public function print($id)
    {
        $sale = $this->saleService->getSaleById($id);
        return view('admin.sales.print', compact('sale'));
    }

    public function pdf($id)
    {
        $sale = $this->saleService->getSaleById($id);
        $pdf = Pdf::loadView('admin.sales.pdf', compact('sale'));
        return $pdf->download('invoice-' . $sale->invoice_number . '.pdf');
    }

    /**
     * Get items for sale dropdown (AJAX)
     */
    public function getItems(Request $request)
    {
        $type = $request->get('type');
        $items = [];

        if ($type === 'product') {
            $products = $this->productService->getAllProducts();
            foreach ($products as $product) {
                if ($product->current_stock > 0) {
                    $items[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'selling_price' => $product->selling_price,
                        'current_stock' => $product->current_stock,
                    ];
                }
            }
        } elseif ($type === 'accessory') {
            $accessories = $this->accessoryService->getAllAccessories();
            foreach ($accessories as $accessory) {
                if ($accessory->current_stock > 0) {
                    $items[] = [
                        'id' => $accessory->id,
                        'name' => $accessory->name,
                        'sku' => $accessory->sku,
                        'selling_price' => $accessory->selling_price,
                        'current_stock' => $accessory->current_stock,
                    ];
                }
            }
        }

        return response()->json($items);
    }
}