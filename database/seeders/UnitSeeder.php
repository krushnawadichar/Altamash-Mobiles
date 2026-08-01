<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Piece', 'code' => 'PCS', 'symbol' => 'pc'],
            ['name' => 'Box', 'code' => 'BOX', 'symbol' => 'box'],
            ['name' => 'Set', 'code' => 'SET', 'symbol' => 'set'],
            ['name' => 'Pair', 'code' => 'PR', 'symbol' => 'pr'],
        ];

        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit['name'],
                'code' => $unit['code'],
                'symbol' => $unit['symbol'],
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}