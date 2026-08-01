<?php

namespace Database\Seeders;

use App\Models\RepairStatus;
use Illuminate\Database\Seeder;

class RepairStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'color' => '#ffc107'],
            ['name' => 'Checking', 'color' => '#17a2b8'],
            ['name' => 'Waiting Parts', 'color' => '#fd7e14'],
            ['name' => 'Repairing', 'color' => '#6f42c1'],
            ['name' => 'Completed', 'color' => '#28a745'],
            ['name' => 'Delivered', 'color' => '#007bff'],
            ['name' => 'Cancelled', 'color' => '#dc3545'],
        ];

        foreach ($statuses as $status) {
            RepairStatus::create([
                'name' => $status['name'],
                'color' => $status['color'],
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }
}