<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ShoeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Add Category
        $shoeCategory = Category::create([
            'name' => 'Shoes',
            'slug' => Str::slug('Shoes'),
            'description' => 'Various kinds of shoes',
            'status' => 1,
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
        ]);

        // Add Brands
        $nike = Brand::create([
            'name' => 'Nike',
            'slug' => Str::slug('Nike'),
            'description' => 'Just do it.',
            'status' => 1,
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/a6/Logo_NIKE.svg',
        ]);

        $adidas = Brand::create([
            'name' => 'Adidas',
            'slug' => Str::slug('Adidas'),
            'description' => 'Impossible is Nothing.',
            'status' => 1,
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/2/20/Adidas_Logo.svg',
        ]);

        $puma = Brand::create([
            'name' => 'Puma',
            'slug' => Str::slug('Puma'),
            'description' => 'Forever Faster.',
            'status' => 1,
            'logo' => 'https://upload.wikimedia.org/wikipedia/en/4/45/Puma_Logo.svg',
        ]);

        // Add Products
        $products = [
            [
                'name' => 'Nike Air Max 270',
                'brand_id' => $nike->id,
                'category_id' => $shoeCategory->id,
                'purchase_price' => 80.00,
                'selling_price' => 150.00,
                'mrp' => 160.00,
                'stock_quantity' => 50,
                'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
                'description' => 'The Nike Air Max 270 delivers visible air under every step.',
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'brand_id' => $adidas->id,
                'category_id' => $shoeCategory->id,
                'purchase_price' => 100.00,
                'selling_price' => 190.00,
                'mrp' => 200.00,
                'stock_quantity' => 30,
                'image' => 'https://images.unsplash.com/photo-1587563871167-1ee9c731aefb?w=800&q=80',
                'description' => 'Say hello to incredible energy return.',
            ],
            [
                'name' => 'Puma RS-X3',
                'brand_id' => $puma->id,
                'category_id' => $shoeCategory->id,
                'purchase_price' => 60.00,
                'selling_price' => 110.00,
                'mrp' => 120.00,
                'stock_quantity' => 45,
                'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&q=80',
                'description' => 'RS-X is back. The future-retro silhouette of this sneaker returns.',
            ],
            [
                'name' => 'Nike React Infinity Run Flyknit 2',
                'brand_id' => $nike->id,
                'category_id' => $shoeCategory->id,
                'purchase_price' => 90.00,
                'selling_price' => 160.00,
                'mrp' => 170.00,
                'stock_quantity' => 25,
                'image' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=800&q=80',
                'description' => 'One of our most tested shoes, designed to help keep you running.',
            ],
            [
                'name' => 'Adidas NMD_R1 V2',
                'brand_id' => $adidas->id,
                'category_id' => $shoeCategory->id,
                'purchase_price' => 70.00,
                'selling_price' => 140.00,
                'mrp' => 150.00,
                'stock_quantity' => 60,
                'image' => 'https://images.unsplash.com/photo-1514989940723-e8e51635b782?w=800&q=80',
                'description' => 'A classic evolution of the NMD franchise.',
            ],
        ];

        foreach ($products as $key => $productData) {
            $image = $productData['image'];
            unset($productData['image']);
            
            $productData['slug'] = Str::slug($productData['name']);
            $productData['sku'] = 'SHOE-' . strtoupper(Str::random(6));
            $productData['barcode'] = 'BAR-' . rand(100000000, 999999999);
            $productData['status'] = 1;
            $productData['tax'] = 0;
            $productData['discount'] = 0;
            $productData['min_stock'] = 10;
            $productData['featured'] = true;
            $productData['new_arrival'] = true;
            $productData['best_seller'] = false;
            
            $product = Product::create($productData);

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $image,
                'is_main' => true,
            ]);
        }
    }
}
