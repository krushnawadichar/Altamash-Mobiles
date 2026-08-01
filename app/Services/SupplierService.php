<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\SupplierRepository;

class SupplierService
{
    protected $supplierRepository;

    public function __construct(SupplierRepository $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function getAllSuppliers()
    {
        return $this->supplierRepository->all();
    }

    public function getActiveSuppliers()
    {
        return $this->supplierRepository->getActive();
    }

    public function getSupplierById($id)
    {
        return $this->supplierRepository->find($id);
    }

    public function createSupplier(array $data)
    {
        $data['created_by'] = auth()->id();
        $data['current_balance'] = $data['opening_balance'] ?? 0;
        return $this->supplierRepository->create($data);
    }

    public function updateSupplier($id, array $data)
    {
        $supplier = $this->supplierRepository->find($id);
        return $this->supplierRepository->update($supplier, $data);
    }

    public function deleteSupplier($id)
    {
        $supplier = $this->supplierRepository->find($id);
        return $this->supplierRepository->delete($supplier);
    }

    public function updateBalance($id, $amount, $type = 'add')
    {
        $supplier = $this->supplierRepository->find($id);
        
        if ($type === 'add') {
            $supplier->current_balance += $amount;
        } else {
            $supplier->current_balance -= $amount;
        }
        
        return $supplier->save();
    }

    public function toggleStatus($id)
    {
        $supplier = $this->supplierRepository->find($id);
        $supplier->is_active = !$supplier->is_active;
        return $supplier->save();
    }
}