<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->all();
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['sku'] = $this->generateSku();
        $data['barcode'] = $this->generateBarcode();
        $data['created_by'] = auth()->id();

        if (isset($data['image']) && $data['image']) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->productRepository->create($data);
    }

    public function updateProduct($id, array $data)
    {
        $product = $this->productRepository->find($id);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->productRepository->update($product, $data);
    }

    public function deleteProduct($id)
    {
        $product = $this->productRepository->find($id);
        
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }

        return $this->productRepository->delete($product);
    }

    public function generateSku()
    {
        $prefix = 'PRD';
        $random = strtoupper(Str::random(8));
        $sku = $prefix . '-' . $random;

        while (Product::where('sku', $sku)->exists()) {
            $random = strtoupper(Str::random(8));
            $sku = $prefix . '-' . $random;
        }

        return $sku;
    }

    public function generateBarcode()
    {
        $barcode = 'BC' . rand(10000000, 99999999);

        while (Product::where('barcode', $barcode)->exists()) {
            $barcode = 'BC' . rand(10000000, 99999999);
        }

        return $barcode;
    }

    public function uploadImage($image)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('products', $filename, 'public');
        return $path;
    }

    public function getLowStockProducts()
    {
        return $this->productRepository->getLowStock();
    }

    public function getOutOfStockProducts()
    {
        return $this->productRepository->getOutOfStock();
    }

    public function updateStock($productId, $quantity, $type)
    {
        $product = $this->productRepository->find($productId);
        
        if ($type === 'add') {
            $product->current_stock += $quantity;
        } elseif ($type === 'subtract') {
            if ($product->current_stock < $quantity) {
                throw new \Exception('Insufficient stock for product: ' . $product->name);
            }
            $product->current_stock -= $quantity;
        }

        return $product->save();
    }

    public function getTotalProducts()
    {
        return $this->productRepository->getTotal();
    }
}