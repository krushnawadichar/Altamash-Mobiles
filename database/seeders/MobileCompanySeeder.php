<?php

namespace Database\Seeders;

use App\Models\MobileCompany;
use Illuminate\Database\Seeder;

class MobileCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'Samsung', 'country' => 'South Korea'],
            ['name' => 'Apple', 'country' => 'USA'],
            ['name' => 'OnePlus', 'country' => 'China'],
            ['name' => 'Xiaomi', 'country' => 'China'],
            ['name' => 'Oppo', 'country' => 'China'],
            ['name' => 'Vivo', 'country' => 'China'],
            ['name' => 'Google', 'country' => 'USA'],
        ];

        foreach ($companies as $company) {
            MobileCompany::create([
                'name' => $company['name'],
                'country' => $company['country'],
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}