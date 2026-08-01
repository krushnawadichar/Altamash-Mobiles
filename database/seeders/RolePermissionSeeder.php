<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $staffRole = Role::create(['name' => 'Staff']);

        // Create Permissions Groups
        $groups = [
            'Dashboard',
            'Categories',
            'Brands',
            'Suppliers',
            'Customers',
            'Products',
            'Accessories',
            'Purchases',
            'Sales',
            'Inventory',
            'Repairs',
            'Expenses',
            'Reports',
            'Users',
            'Settings'
        ];

        $actions = ['View', 'Create', 'Edit', 'Delete'];

        foreach ($groups as $group) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => $action . ' ' . $group,
                    'group' => $group
                ]);
            }
        }

        // Assign all permissions to Admin
        $adminRole->permissions()->attach(Permission::all());

        // Assign specific permissions to Manager
        $managerPermissions = Permission::whereNotIn('name', ['Delete Users', 'Settings'])->get();
        $managerRole->permissions()->attach($managerPermissions);

        // Assign basic permissions to Staff
        $staffPermissions = Permission::whereIn('name', [
            'View Dashboard',
            'View Products',
            'View Purchases',
            'View Sales',
            'View Customers',
            'View Suppliers'
        ])->get();
        $staffRole->permissions()->attach($staffPermissions);
    }
}