<?php

namespace App\Services;

use App\Models\RepairStatus;
use App\Repositories\RepairStatusRepository;

class RepairStatusService
{
    protected $repairStatusRepository;

    public function __construct(RepairStatusRepository $repairStatusRepository)
    {
        $this->repairStatusRepository = $repairStatusRepository;
    }

    public function getAllRepairStatuses()
    {
        return $this->repairStatusRepository->all();
    }

    public function getActiveRepairStatuses()
    {
        return $this->repairStatusRepository->getActive();
    }

    public function getRepairStatusById($id)
    {
        return $this->repairStatusRepository->find($id);
    }

    public function createRepairStatus(array $data)
    {
        $data['created_by'] = auth()->id();
        return $this->repairStatusRepository->create($data);
    }

    public function updateRepairStatus($id, array $data)
    {
        $repairStatus = $this->repairStatusRepository->find($id);
        return $this->repairStatusRepository->update($repairStatus, $data);
    }

    public function deleteRepairStatus($id)
    {
        $repairStatus = $this->repairStatusRepository->find($id);
        return $this->repairStatusRepository->delete($repairStatus);
    }

    public function toggleStatus($id)
    {
        $repairStatus = $this->repairStatusRepository->find($id);
        $repairStatus->is_active = !$repairStatus->is_active;
        return $repairStatus->save();
    }
}