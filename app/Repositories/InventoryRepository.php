<?php

namespace App\Repositories;

use App\Models\Inventory;

class InventoryRepository extends BaseRepository
{
    public function __construct(Inventory $inventory)
    {
        parent::__construct($inventory);
    }

    public function getByProduct($productId, $productType = 'App\Models\Product')
    {
        return $this->model->where('inventoriable_id', $productId)
                          ->where('inventoriable_type', $productType)
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getByType($type)
    {
        return $this->model->where('type', $type)
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getByDateRange($startDate, $endDate)
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getRecentTransactions($limit = 50)
    {
        return $this->model->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    public function getStockMovements($inventoriableId, $inventoriableType, $limit = 20)
    {
        return $this->model->where('inventoriable_id', $inventoriableId)
                          ->where('inventoriable_type', $inventoriableType)
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    public function getTotalInventoryValue()
    {
        return $this->model->sum('total_price');
    }

    public function getInventoryByReference($referenceId, $referenceType)
    {
        return $this->model->where('reference_id', $referenceId)
                          ->where('reference_type', $referenceType)
                          ->get();
    }
}