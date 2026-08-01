<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Services\BrandService;
use App\Services\SupplierService;
use App\Services\UnitService;
use App\Services\ProductTypeService;
use App\Services\MobileCompanyService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $brandService;
    protected $supplierService;
    protected $unitService;
    protected $productTypeService;
    protected $mobileCompanyService;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        BrandService $brandService,
        SupplierService $supplierService,
        UnitService $unitService,
        ProductTypeService $productTypeService,
        MobileCompanyService $mobileCompanyService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->supplierService = $supplierService;
        $this->unitService = $unitService;
        $this->productTypeService = $productTypeService;
        $this->mobileCompanyService = $mobileCompanyService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getAllProducts();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = $this->categoryService->getActiveCategories();
        $brands = $this->brandService->getActiveBrands();
        $suppliers = $this->supplierService->getActiveSuppliers();
        $units = $this->unitService->getActiveUnits();
        $productTypes = $this->productTypeService->getActiveProductTypes();
        $mobileCompanies = $this->mobileCompanyService->getActiveMobileCompanies();

        return view('admin.products.create', compact(
            'categories',
            'brands',
            'suppliers',
            'units',
            'productTypes',
            'mobileCompanies'
        ));
    }

    public function store(ProductRequest $request)
    {
        $this->productService->createProduct($request->validated());
        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productService->getProductById($id);
        $categories = $this->categoryService->getActiveCategories();
        $brands = $this->brandService->getActiveBrands();
        $suppliers = $this->supplierService->getActiveSuppliers();
        $units = $this->unitService->getActiveUnits();
        $productTypes = $this->productTypeService->getActiveProductTypes();
        $mobileCompanies = $this->mobileCompanyService->getActiveMobileCompanies();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'brands',
            'suppliers',
            'units',
            'productTypes',
            'mobileCompanies'
        ));
    }


    public function update(ProductRequest $request, $id)
    {
        $this->productService->updateProduct($id, $request->validated());
        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function generateBarcode($id)
    {
        $product = $this->productService->getProductById($id);
        return view('admin.products.barcode', compact('product'));
    }
    
}