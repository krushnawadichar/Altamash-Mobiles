<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sale;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Sales');
    }

    public function view(User $user, Sale $sale)
    {
        return $user->hasPermission('View Sales');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Sales');
    }

    public function update(User $user, Sale $sale)
    {
        return $user->hasPermission('Edit Sales');
    }

    public function delete(User $user, Sale $sale)
    {
        return $user->hasPermission('Delete Sales');
    }

    public function restore(User $user, Sale $sale)
    {
        return $user->hasPermission('Delete Sales');
    }

    public function forceDelete(User $user, Sale $sale)
    {
        return $user->hasPermission('Delete Sales');
    }
}