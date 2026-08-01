<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Shop Rent',
            'Electricity',
            'Internet',
            'Salary',
            'Tea',
            'Petrol',
            'Maintenance',
            'Marketing',
            'Other Expenses',
        ];

        foreach ($categories as $category) {
            ExpenseCategory::create([
                'name' => $category,
                'slug' => Str::slug($category),
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}