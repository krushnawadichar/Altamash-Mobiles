<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sku' => 'PRD-' . strtoupper(Str::random(8)),
            'purchase_price' => $this->faker->randomFloat(2, 10000, 100000),
            'selling_price' => $this->faker->randomFloat(2, 12000, 120000),
            'current_stock' => $this->faker->numberBetween(0, 100),
            'minimum_stock' => 5,
            'is_active' => true,
        ];
    }
}