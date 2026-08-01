<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    public function getRoleById($id)
    {
        return Role::with('permissions')->find($id);
    }

    public function getRoleByName($name)
    {
        return Role::where('name', $name)->first();
    }

    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->permissions()->attach($data['permissions']);
            }

            return $role;
        });
    }

    public function updateRole($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $role = Role::find($id);
            $role->update([
                'name' => $data['name'],
            ]);

            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }

            return $role;
        });
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        $role->permissions()->detach();
        return $role->delete();
    }

    public function getAllPermissions()
    {
        return Permission::all();
    }

    public function getPermissionsGrouped()
    {
        return Permission::all()->groupBy('group');
    }

    public function getRolePermissions($roleId)
    {
        $role = Role::find($roleId);
        return $role ? $role->permissions->pluck('id')->toArray() : [];
    }

    public function assignPermissions($roleId, array $permissionIds)
    {
        $role = Role::find($roleId);
        $role->permissions()->sync($permissionIds);
        return $role;
    }

    public function getDefaultRoles()
    {
        return [
            'Admin' => 'Full access to all features',
            'Manager' => 'Access to most features except user management',
            'Staff' => 'Basic access to sales, purchases, and inventory',
        ];
    }

    public function getDefaultPermissions()
    {
        $groups = [
            'Dashboard' => ['View Dashboard'],
            'Categories' => ['View Categories', 'Create Categories', 'Edit Categories', 'Delete Categories'],
            'Brands' => ['View Brands', 'Create Brands', 'Edit Brands', 'Delete Brands'],
            'Suppliers' => ['View Suppliers', 'Create Suppliers', 'Edit Suppliers', 'Delete Suppliers'],
            'Customers' => ['View Customers', 'Create Customers', 'Edit Customers', 'Delete Customers'],
            'Products' => ['View Products', 'Create Products', 'Edit Products', 'Delete Products'],
            'Accessories' => ['View Accessories', 'Create Accessories', 'Edit Accessories', 'Delete Accessories'],
            'Purchases' => ['View Purchases', 'Create Purchases', 'Edit Purchases', 'Delete Purchases'],
            'Sales' => ['View Sales', 'Create Sales', 'Edit Sales', 'Delete Sales'],
            'Inventory' => ['View Inventory', 'Create Inventory', 'Edit Inventory', 'Delete Inventory'],
            'Repairs' => ['View Repairs', 'Create Repairs', 'Edit Repairs', 'Delete Repairs'],
            'Expenses' => ['View Expenses', 'Create Expenses', 'Edit Expenses', 'Delete Expenses'],
            'Reports' => ['View Reports'],
            'Users' => ['View Users', 'Create Users', 'Edit Users', 'Delete Users'],
            'Settings' => ['View Settings', 'Edit Settings'],
        ];

        return $groups;
    }

    public function createDefaultPermissions()
    {
        $groups = $this->getDefaultPermissions();
        
        foreach ($groups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ], [
                    'group' => $group,
                ]);
            }
        }
    }

    public function createDefaultRoles()
    {
        // Create Admin Role
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        // Assign all permissions to Admin
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Create Manager Role
        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web',
        ]);

        // Assign limited permissions to Manager
        $managerPermissions = Permission::whereNotIn('name', ['Delete Users', 'Edit Settings', 'Delete Settings'])->get();
        $managerRole->permissions()->sync($managerPermissions->pluck('id'));

        // Create Staff Role
        $staffRole = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => 'web',
        ]);

        // Assign basic permissions to Staff
        $staffPermissions = Permission::whereIn('name', [
            'View Dashboard',
            'View Categories',
            'View Brands',
            'View Suppliers',
            'View Customers',
            'View Products',
            'View Accessories',
            'View Purchases',
            'View Sales',
            'View Inventory',
            'View Repairs',
            'View Expenses',
        ])->get();
        $staffRole->permissions()->sync($staffPermissions->pluck('id'));

        return [
            'admin' => $adminRole,
            'manager' => $managerRole,
            'staff' => $staffRole,
        ];
    }

    public function getRoleStatistics()
    {
        $total = Role::count();
        $adminCount = Role::where('name', 'Admin')->first()?->users()->count() ?? 0;
        $managerCount = Role::where('name', 'Manager')->first()?->users()->count() ?? 0;
        $staffCount = Role::where('name', 'Staff')->first()?->users()->count() ?? 0;

        return [
            'total_roles' => $total,
            'admin_users' => $adminCount,
            'manager_users' => $managerCount,
            'staff_users' => $staffCount,
        ];
    }

    public function getRoleWithUsers($roleId)
    {
        return Role::with('users')->find($roleId);
    }
}