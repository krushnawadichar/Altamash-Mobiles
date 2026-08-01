<?php

namespace App\Repositories;

use App\Models\Unit;

class UnitRepository extends BaseRepository
{
    public function __construct(Unit $unit)
    {
        parent::__construct($unit);
    }

    public function getByCode($code)
    {
        return $this->model->where('code', $code)->first();
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('code', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function toggleStatus($id)
    {
        $unit = $this->find($id);
        if ($unit) {
            $unit->is_active = !$unit->is_active;
            $unit->save();
            return $unit;
        }
        return null;
    }
}