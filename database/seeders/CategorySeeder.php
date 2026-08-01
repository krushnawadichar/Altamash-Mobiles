<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Mobile Phones',
            'Accessories',
            'Tablets',
            'Smart Watches',
            'Laptops',
            'Headphones',
            'Speakers',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}