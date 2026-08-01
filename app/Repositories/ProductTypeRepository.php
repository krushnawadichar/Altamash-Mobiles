<?php

namespace App\Repositories;

use App\Models\ProductType;

class ProductTypeRepository extends BaseRepository
{
    public function __construct(ProductType $productType)
    {
        parent::__construct($productType);
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function toggleStatus($id)
    {
        $productType = $this->find($id);
        if ($productType) {
            $productType->is_active = !$productType->is_active;
            $productType->save();
            return $productType;
        }
        return null;
    }
}