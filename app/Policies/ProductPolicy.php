<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Products');
    }

    public function view(User $user, Product $product)
    {
        return $user->hasPermission('View Products');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Products');
    }

    public function update(User $user, Product $product)
    {
        return $user->hasPermission('Edit Products');
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasPermission('Delete Products');
    }

    public function restore(User $user, Product $product)
    {
        return $user->hasPermission('Delete Products');
    }

    public function forceDelete(User $user, Product $product)
    {
        return $user->hasPermission('Delete Products');
    }
}