<?php

namespace App\Services;

use App\Models\Brand;
use App\Repositories\BrandRepository;
use Illuminate\Support\Str;

class BrandService
{
    protected $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getAllBrands()
    {
        return $this->brandRepository->all();
    }

    public function getActiveBrands()
    {
        return $this->brandRepository->getActive();
    }

    public function getBrandById($id)
    {
        return $this->brandRepository->find($id);
    }

    public function createBrand(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = auth()->id();

        return $this->brandRepository->create($data);
    }

    public function updateBrand($id, array $data)
    {
        $brand = $this->brandRepository->find($id);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->brandRepository->update($brand, $data);
    }

    public function deleteBrand($id)
    {
        $brand = $this->brandRepository->find($id);
        return $this->brandRepository->delete($brand);
    }
}