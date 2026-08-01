<?php

namespace App\Services;

use App\Models\MobileCompany;
use App\Repositories\MobileCompanyRepository;

class MobileCompanyService
{
    protected $mobileCompanyRepository;

    public function __construct(MobileCompanyRepository $mobileCompanyRepository)
    {
        $this->mobileCompanyRepository = $mobileCompanyRepository;
    }

    public function getAllMobileCompanies()
    {
        return $this->mobileCompanyRepository->all();
    }

    public function getActiveMobileCompanies()
    {
        return $this->mobileCompanyRepository->getActive();
    }

    public function getMobileCompanyById($id)
    {
        return $this->mobileCompanyRepository->find($id);
    }

    public function createMobileCompany(array $data)
    {
        $data['created_by'] = auth()->id();
        return $this->mobileCompanyRepository->create($data);
    }

    public function updateMobileCompany($id, array $data)
    {
        $mobileCompany = $this->mobileCompanyRepository->find($id);
        return $this->mobileCompanyRepository->update($mobileCompany, $data);
    }

    public function deleteMobileCompany($id)
    {
        $mobileCompany = $this->mobileCompanyRepository->find($id);
        return $this->mobileCompanyRepository->delete($mobileCompany);
    }

    public function toggleStatus($id)
    {
        $mobileCompany = $this->mobileCompanyRepository->find($id);
        $mobileCompany->is_active = !$mobileCompany->is_active;
        return $mobileCompany->save();
    }
}