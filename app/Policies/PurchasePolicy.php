<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Purchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Purchases');
    }

    public function view(User $user, Purchase $purchase)
    {
        return $user->hasPermission('View Purchases');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Purchases');
    }

    public function update(User $user, Purchase $purchase)
    {
        return $user->hasPermission('Edit Purchases');
    }

    public function delete(User $user, Purchase $purchase)
    {
        return $user->hasPermission('Delete Purchases');
    }

    public function restore(User $user, Purchase $purchase)
    {
        return $user->hasPermission('Delete Purchases');
    }

    public function forceDelete(User $user, Purchase $purchase)
    {
        return $user->hasPermission('Delete Purchases');
    }
}