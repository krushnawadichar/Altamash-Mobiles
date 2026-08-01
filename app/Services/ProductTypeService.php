<?php

namespace App\Services;

use App\Models\ProductType;
use App\Repositories\ProductTypeRepository;
use Illuminate\Support\Str;

class ProductTypeService
{
    protected $productTypeRepository;

    public function __construct(ProductTypeRepository $productTypeRepository)
    {
        $this->productTypeRepository = $productTypeRepository;
    }

    public function getAllProductTypes()
    {
        return $this->productTypeRepository->all();
    }

    public function getActiveProductTypes()
    {
        return $this->productTypeRepository->getActive();
    }

    public function getProductTypeById($id)
    {
        return $this->productTypeRepository->find($id);
    }

    public function createProductType(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = auth()->id();
        return $this->productTypeRepository->create($data);
    }

    public function updateProductType($id, array $data)
    {
        $productType = $this->productTypeRepository->find($id);
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return $this->productTypeRepository->update($productType, $data);
    }

    public function deleteProductType($id)
    {
        $productType = $this->productTypeRepository->find($id);
        return $this->productTypeRepository->delete($productType);
    }

    public function toggleStatus($id)
    {
        $productType = $this->productTypeRepository->find($id);
        $productType->is_active = !$productType->is_active;
        return $productType->save();
    }
}