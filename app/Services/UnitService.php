<?php

namespace App\Services;

use App\Models\Unit;
use App\Repositories\UnitRepository;

class UnitService
{
    protected $unitRepository;

    public function __construct(UnitRepository $unitRepository)
    {
        $this->unitRepository = $unitRepository;
    }

    public function getAllUnits()
    {
        return $this->unitRepository->all();
    }

    public function getActiveUnits()
    {
        return $this->unitRepository->getActive();
    }

    public function getUnitById($id)
    {
        return $this->unitRepository->find($id);
    }

    public function createUnit(array $data)
    {
        $data['created_by'] = auth()->id();
        return $this->unitRepository->create($data);
    }

    public function updateUnit($id, array $data)
    {
        $unit = $this->unitRepository->find($id);
        return $this->unitRepository->update($unit, $data);
    }

    public function deleteUnit($id)
    {
        $unit = $this->unitRepository->find($id);
        return $this->unitRepository->delete($unit);
    }

    public function toggleStatus($id)
    {
        $unit = $this->unitRepository->find($id);
        $unit->is_active = !$unit->is_active;
        return $unit->save();
    }
}