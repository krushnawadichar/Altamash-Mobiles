<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function getBySku($sku)
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function getByBarcode($barcode)
    {
        return $this->model->where('barcode', $barcode)->first();
    }

    public function getByImei($imei)
    {
        return $this->model->where('imei', $imei)->first();
    }

    public function getWithRelations()
    {
        return $this->model->with(['category', 'brand', 'supplier', 'unit', 'productType', 'mobileCompany'])->get();
    }

    public function getActiveWithRelations()
    {
        return $this->model->where('is_active', true)
                          ->with(['category', 'brand', 'supplier', 'unit', 'productType', 'mobileCompany'])
                          ->get();
    }

    public function getLowStock()
    {
        return $this->model->whereColumn('current_stock', '<=', 'minimum_stock')
                          ->where('is_active', true)
                          ->get();
    }

    public function getOutOfStock()
    {
        return $this->model->where('current_stock', '<=', 0)
                          ->where('is_active', true)
                          ->get();
    }

    public function getInStock()
    {
        return $this->model->where('current_stock', '>', 0)
                          ->where('is_active', true)
                          ->get();
    }

    public function getByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)
                          ->where('is_active', true)
                          ->get();
    }

    public function getByBrand($brandId)
    {
        return $this->model->where('brand_id', $brandId)
                          ->where('is_active', true)
                          ->get();
    }

    public function getBySupplier($supplierId)
    {
        return $this->model->where('supplier_id', $supplierId)
                          ->where('is_active', true)
                          ->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('sku', 'like', "%{$query}%")
                          ->orWhere('barcode', 'like', "%{$query}%")
                          ->orWhere('imei', 'like', "%{$query}%")
                          ->where('is_active', true)
                          ->paginate(15);
    }

    public function filter($filters)
    {
        $query = $this->model->query();

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['min_price'])) {
            $query->where('selling_price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('selling_price', '<=', $filters['max_price']);
        }

        if (isset($filters['stock_status'])) {
            if ($filters['stock_status'] === 'low') {
                $query->whereColumn('current_stock', '<=', 'minimum_stock');
            } elseif ($filters['stock_status'] === 'out') {
                $query->where('current_stock', '<=', 0);
            } elseif ($filters['stock_status'] === 'in') {
                $query->where('current_stock', '>', 0);
            }
        }

        return $query->paginate(15);
    }

    public function getTotal()
    {
        return $this->model->count();
    }

    public function getTotalValue()
    {
        return $this->model->sum('current_stock * purchase_price');
    }
}