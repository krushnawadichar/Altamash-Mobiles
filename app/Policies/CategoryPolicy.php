<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Categories');
    }

    public function view(User $user, Category $category)
    {
        return $user->hasPermission('View Categories');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Categories');
    }

    public function update(User $user, Category $category)
    {
        return $user->hasPermission('Edit Categories');
    }

    public function delete(User $user, Category $category)
    {
        return $user->hasPermission('Delete Categories');
    }

    public function restore(User $user, Category $category)
    {
        return $user->hasPermission('Delete Categories');
    }

    public function forceDelete(User $user, Category $category)
    {
        return $user->hasPermission('Delete Categories');
    }
}