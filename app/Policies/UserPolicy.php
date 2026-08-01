<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Users');
    }

    public function view(User $user, User $model)
    {
        return $user->hasPermission('View Users');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Users');
    }

    public function update(User $user, User $model)
    {
        return $user->hasPermission('Edit Users');
    }

    public function delete(User $user, User $model)
    {
        return $user->hasPermission('Delete Users');
    }

    public function restore(User $user, User $model)
    {
        return $user->hasPermission('Delete Users');
    }

    public function forceDelete(User $user, User $model)
    {
        return $user->hasPermission('Delete Users');
    }
}