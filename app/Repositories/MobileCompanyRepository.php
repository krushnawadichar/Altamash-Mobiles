<?php

namespace App\Repositories;

use App\Models\MobileCompany;

class MobileCompanyRepository extends BaseRepository
{
    public function __construct(MobileCompany $mobileCompany)
    {
        parent::__construct($mobileCompany);
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getByCountry($country)
    {
        return $this->model->where('country', $country)->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('country', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function toggleStatus($id)
    {
        $mobileCompany = $this->find($id);
        if ($mobileCompany) {
            $mobileCompany->is_active = !$mobileCompany->is_active;
            $mobileCompany->save();
            return $mobileCompany;
        }
        return null;
    }
}