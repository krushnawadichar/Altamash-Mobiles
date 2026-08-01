<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index()
    {
        $brands = $this->brandService->getAllBrands();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $this->brandService->createBrand($request->validated());
        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit($id)
    {
        $brand = $this->brandService->getBrandById($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, $id)
    {
        $this->brandService->updateBrand($id, $request->validated());
        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $this->brandService->deleteBrand($id);
        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->brandService->toggleStatus($id);
        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand status updated successfully.');
    }
}