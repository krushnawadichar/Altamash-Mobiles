<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Samsung', 'Sony', 'Apple', 'LG', 'Panasonic', 'Dell', 'HP', 'Lenovo', 'Bose', 'Canon', 
            'Google', 'OnePlus', 'Asus', 'TCL', 'Sennheiser', 'Garmin', 'Nikon', 'Dyson', 'Philips'
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(['name' => $b, 'slug' => Str::slug($b)]);
        }

        $categories = [
            'Smartphones', 'Laptops', 'Televisions', 'Audio & Headphones', 'Smartwatches', 'Cameras', 'Home Appliances'
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat, 'slug' => Str::slug($cat)]);
        }

        $products = [
            // Smartphones
            ['name' => 'iPhone 15 Pro Max', 'category' => 'Smartphones', 'brand' => 'Apple', 'price' => 145000, 'mrp' => 159900, 'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Samsung Galaxy S24 Ultra', 'category' => 'Smartphones', 'brand' => 'Samsung', 'price' => 129999, 'mrp' => 134999, 'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Google Pixel 8 Pro', 'category' => 'Smartphones', 'brand' => 'Google', 'price' => 106999, 'mrp' => 106999, 'image' => 'https://images.unsplash.com/photo-1598327105666-5b89351cb315?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'OnePlus 12 5G', 'category' => 'Smartphones', 'brand' => 'OnePlus', 'price' => 64999, 'mrp' => 69999, 'image' => 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'iPhone 14 (128GB)', 'category' => 'Smartphones', 'brand' => 'Apple', 'price' => 65999, 'mrp' => 79900, 'image' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=800&q=80'],
            
            // Laptops
            ['name' => 'MacBook Pro 16" M3 Max', 'category' => 'Laptops', 'brand' => 'Apple', 'price' => 319900, 'mrp' => 319900, 'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Dell XPS 15', 'category' => 'Laptops', 'brand' => 'Dell', 'price' => 185000, 'mrp' => 195000, 'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'MacBook Air M2', 'category' => 'Laptops', 'brand' => 'Apple', 'price' => 114900, 'mrp' => 114900, 'image' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Asus ROG Zephyrus G14', 'category' => 'Laptops', 'brand' => 'Asus', 'price' => 145990, 'mrp' => 165990, 'image' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Lenovo ThinkPad X1 Carbon', 'category' => 'Laptops', 'brand' => 'Lenovo', 'price' => 155000, 'mrp' => 170000, 'image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=800&q=80'],

            // Televisions
            ['name' => 'Sony Bravia XR 65" OLED', 'category' => 'Televisions', 'brand' => 'Sony', 'price' => 249990, 'mrp' => 349990, 'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'LG 55" 4K Smart TV', 'category' => 'Televisions', 'brand' => 'LG', 'price' => 85000, 'mrp' => 110000, 'image' => 'https://images.unsplash.com/photo-1577979749830-f1d742b96791?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Samsung 75" Neo QLED 8K', 'category' => 'Televisions', 'brand' => 'Samsung', 'price' => 389000, 'mrp' => 450000, 'image' => 'https://images.unsplash.com/photo-1461151304267-38535e780c79?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'TCL 50" 4K Ultra HD', 'category' => 'Televisions', 'brand' => 'TCL', 'price' => 32990, 'mrp' => 45990, 'image' => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?auto=format&fit=crop&w=800&q=80'],

            // Audio & Headphones
            ['name' => 'Sony WH-1000XM5', 'category' => 'Audio & Headphones', 'brand' => 'Sony', 'price' => 29990, 'mrp' => 34990, 'image' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Apple AirPods Pro (2nd Gen)', 'category' => 'Audio & Headphones', 'brand' => 'Apple', 'price' => 24900, 'mrp' => 24900, 'image' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Bose QuietComfort Ultra', 'category' => 'Audio & Headphones', 'brand' => 'Bose', 'price' => 35900, 'mrp' => 35900, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Sennheiser Momentum 4', 'category' => 'Audio & Headphones', 'brand' => 'Sennheiser', 'price' => 27990, 'mrp' => 34990, 'image' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80'],

            // Smartwatches
            ['name' => 'Apple Watch Series 9', 'category' => 'Smartwatches', 'brand' => 'Apple', 'price' => 41900, 'mrp' => 41900, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Samsung Galaxy Watch 6', 'category' => 'Smartwatches', 'brand' => 'Samsung', 'price' => 29999, 'mrp' => 33999, 'image' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Garmin Fenix 7 Pro', 'category' => 'Smartwatches', 'brand' => 'Garmin', 'price' => 84990, 'mrp' => 89990, 'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=800&q=80'],

            // Cameras
            ['name' => 'Sony Alpha 7 IV Mirrorless', 'category' => 'Cameras', 'brand' => 'Sony', 'price' => 242990, 'mrp' => 260000, 'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Canon EOS R5 Mirrorless', 'category' => 'Cameras', 'brand' => 'Canon', 'price' => 275000, 'mrp' => 290000, 'image' => 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Nikon Z8 Body', 'category' => 'Cameras', 'brand' => 'Nikon', 'price' => 310000, 'mrp' => 340000, 'image' => 'https://images.unsplash.com/photo-1500634245200-e5245c7574ef?auto=format&fit=crop&w=800&q=80'],

            // Home Appliances
            ['name' => 'Panasonic Front Load Washing Machine', 'category' => 'Home Appliances', 'brand' => 'Panasonic', 'price' => 45000, 'mrp' => 52000, 'image' => 'https://images.unsplash.com/photo-1626806787461-102c1bfaaea1?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'LG 600L Double Door Refrigerator', 'category' => 'Home Appliances', 'brand' => 'LG', 'price' => 89000, 'mrp' => 105000, 'image' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Dyson V15 Detect Vacuum', 'category' => 'Home Appliances', 'brand' => 'Dyson', 'price' => 65900, 'mrp' => 69900, 'image' => 'https://images.unsplash.com/photo-1558317374-067fb5f30001?auto=format&fit=crop&w=800&q=80'],
            ['name' => 'Philips Essential Air Fryer', 'category' => 'Home Appliances', 'brand' => 'Philips', 'price' => 8490, 'mrp' => 12990, 'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?auto=format&fit=crop&w=800&q=80'],
        ];

        foreach ($products as $p) {
            $cat = Category::where('name', $p['category'])->first();
            $brand = Brand::where('name', $p['brand'])->first();
            
            $product = Product::firstOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'category_id' => $cat->id ?? null,
                    'brand_id' => $brand->id ?? null,
                    'name' => $p['name'],
                    'sku' => 'SKU-' . strtoupper(Str::random(8)),
                    'barcode' => rand(100000000000, 999999999999),
                    'short_description' => 'A premium ' . $p['name'] . ' built for performance and durability.',
                    'description' => 'Experience the cutting-edge technology with the ' . $p['name'] . '. Designed perfectly by ' . $p['brand'] . ', this premium device offers incredible value, unmatched quality, and top-tier reliability. Perfect for your day-to-day lifestyle.',
                    'purchase_price' => $p['price'] * 0.7,
                    'selling_price' => $p['price'],
                    'mrp' => $p['mrp'],
                    'stock_quantity' => rand(10, 100),
                    'status' => 1,
                    'featured' => (rand(1, 10) > 7) ? 1 : 0, // 30% chance to be featured
                ]
            );

            // Add main image
            DB::table('product_images')->updateOrInsert(
                ['product_id' => $product->id, 'is_main' => 1],
                ['image' => $p['image'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
