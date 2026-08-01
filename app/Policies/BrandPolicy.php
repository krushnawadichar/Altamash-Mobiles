<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Brand;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Brands');
    }

    public function view(User $user, Brand $brand)
    {
        return $user->hasPermission('View Brands');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Brands');
    }

    public function update(User $user, Brand $brand)
    {
        return $user->hasPermission('Edit Brands');
    }

    public function delete(User $user, Brand $brand)
    {
        return $user->hasPermission('Delete Brands');
    }

    public function restore(User $user, Brand $brand)
    {
        return $user->hasPermission('Delete Brands');
    }

    public function forceDelete(User $user, Brand $brand)
    {
        return $user->hasPermission('Delete Brands');
    }
}