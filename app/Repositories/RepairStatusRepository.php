<?php

namespace App\Repositories;

use App\Models\RepairStatus;

class RepairStatusRepository extends BaseRepository
{
    public function __construct(RepairStatus $repairStatus)
    {
        parent::__construct($repairStatus);
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getByName($name)
    {
        return $this->model->where('name', $name)->first();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function toggleStatus($id)
    {
        $repairStatus = $this->find($id);
        if ($repairStatus) {
            $repairStatus->is_active = !$repairStatus->is_active;
            $repairStatus->save();
            return $repairStatus;
        }
        return null;
    }
}