<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'New Phone',
            'Used Phone',
            'Refurbished',
            'Accessory',
            'Spare Part',
        ];

        foreach ($types as $type) {
            ProductType::create([
                'name' => $type,
                'slug' => Str::slug($type),
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}