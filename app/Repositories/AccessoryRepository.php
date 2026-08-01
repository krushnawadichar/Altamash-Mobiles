<?php

namespace App\Repositories;

use App\Models\Accessory;

class AccessoryRepository extends BaseRepository
{
    public function __construct(Accessory $accessory)
    {
        parent::__construct($accessory);
    }

    public function getBySku($sku)
    {
        return $this->model->where('sku', $sku)->first();
    }

    public function getByBarcode($barcode)
    {
        return $this->model->where('barcode', $barcode)->first();
    }

    public function getWithSupplier()
    {
        return $this->model->with('supplier')->get();
    }

    public function getActiveWithSupplier()
    {
        return $this->model->where('is_active', true)->with('supplier')->get();
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

    public function getByType($type)
    {
        return $this->model->where('type', $type)
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
                          ->orWhere('type', 'like', "%{$query}%")
                          ->where('is_active', true)
                          ->paginate(15);
    }

    public function getTypes()
    {
        return $this->model->distinct()->pluck('type');
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