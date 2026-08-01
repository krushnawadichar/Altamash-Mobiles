<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('View Customers');
    }

    public function view(User $user, Customer $customer)
    {
        return $user->hasPermission('View Customers');
    }

    public function create(User $user)
    {
        return $user->hasPermission('Create Customers');
    }

    public function update(User $user, Customer $customer)
    {
        return $user->hasPermission('Edit Customers');
    }

    public function delete(User $user, Customer $customer)
    {
        return $user->hasPermission('Delete Customers');
    }

    public function restore(User $user, Customer $customer)
    {
        return $user->hasPermission('Delete Customers');
    }

    public function forceDelete(User $user, Customer $customer)
    {
        return $user->hasPermission('Delete Customers');
    }
}