<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Suppliers');
    }

    public function view(User $user, Supplier $supplier)
    {
        return $user->hasPermission('View Suppliers');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Suppliers');
    }

    public function update(User $user, Supplier $supplier)
    {
        return $user->hasPermission('Edit Suppliers');
    }

    public function delete(User $user, Supplier $supplier)
    {
        return $user->hasPermission('Delete Suppliers');
    }

    public function restore(User $user, Supplier $supplier)
    {
        return $user->hasPermission('Delete Suppliers');
    }

    public function forceDelete(User $user, Supplier $supplier)
    {
        return $user->hasPermission('Delete Suppliers');
    }
}