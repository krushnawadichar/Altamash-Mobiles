<?php

namespace App\Services;

use App\Models\Accessory;
use App\Repositories\AccessoryRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AccessoryService
{
    protected $accessoryRepository;

    public function __construct(AccessoryRepository $accessoryRepository)
    {
        $this->accessoryRepository = $accessoryRepository;
    }

    public function getAllAccessories()
    {
        return $this->accessoryRepository->all();
    }

    public function getAccessoryById($id)
    {
        return $this->accessoryRepository->find($id);
    }

    public function createAccessory(array $data)
    {
        $data['sku'] = $this->generateSku();
        $data['barcode'] = $this->generateBarcode();
        $data['created_by'] = auth()->id();

        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->accessoryRepository->create($data);
    }

    public function updateAccessory($id, array $data)
    {
        $accessory = $this->accessoryRepository->find($id);

        if (isset($data['image']) && $data['image']) {
            if ($accessory->image) {
                Storage::delete('public/' . $accessory->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->accessoryRepository->update($accessory, $data);
    }

    public function deleteAccessory($id)
    {
        $accessory = $this->accessoryRepository->find($id);
        if ($accessory->image) {
            Storage::delete('public/' . $accessory->image);
        }
        return $this->accessoryRepository->delete($accessory);
    }

    public function generateSku()
    {
        $prefix = 'ACC';
        $random = strtoupper(Str::random(8));
        $sku = $prefix . '-' . $random;

        while (Accessory::where('sku', $sku)->exists()) {
            $random = strtoupper(Str::random(8));
            $sku = $prefix . '-' . $random;
        }

        return $sku;
    }

    public function generateBarcode()
    {
        $barcode = 'BC' . rand(10000000, 99999999);
        while (Accessory::where('barcode', $barcode)->exists()) {
            $barcode = 'BC' . rand(10000000, 99999999);
        }
        return $barcode;
    }

    public function uploadImage($image)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('accessories', $filename, 'public');
        return $path;
    }

    public function updateStock($id, $quantity, $type)
    {
        $accessory = $this->accessoryRepository->find($id);
        
        if ($type === 'add') {
            $accessory->current_stock += $quantity;
        } elseif ($type === 'subtract') {
            if ($accessory->current_stock < $quantity) {
                throw new \Exception('Insufficient stock for accessory: ' . $accessory->name);
            }
            $accessory->current_stock -= $quantity;
        }

        return $accessory->save();
    }

    public function getLowStockAccessories()
    {
        return $this->accessoryRepository->getLowStock();
    }

    public function getOutOfStockAccessories()
    {
        return $this->accessoryRepository->getOutOfStock();
    }

    public function getActiveAccessories()
    {
        return $this->accessoryRepository->getActive();
    }
}