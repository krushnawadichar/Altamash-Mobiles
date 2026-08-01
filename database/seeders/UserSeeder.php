<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@altamashmobiles.com',
            'password' => Hash::make('password'),
            'phone' => '0300-1234567',
            'address' => 'Main Market, Lahore',
            'is_active' => true,
            'role_id' => 1,
        ]);

        // Manager User
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@altamashmobiles.com',
            'password' => Hash::make('password'),
            'phone' => '0300-7654321',
            'address' => 'Gulberg, Lahore',
            'is_active' => true,
            'role_id' => 2,
        ]);

        // Staff User
        User::create([
            'name' => 'Staff User',
            'email' => 'staff@altamashmobiles.com',
            'password' => Hash::make('password'),
            'phone' => '0300-9876543',
            'address' => 'Model Town, Lahore',
            'is_active' => true,
            'role_id' => 3,
        ]);
    }
}