<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getWithRole()
    {
        return $this->model->with('role')->get();
    }

    public function getActiveWithRole()
    {
        return $this->model->where('is_active', true)->with('role')->get();
    }

    public function getByRole($roleId)
    {
        return $this->model->where('role_id', $roleId)->with('role')->get();
    }

    public function getAdmins()
    {
        return $this->model->where('role_id', 1)->get();
    }

    public function getManagers()
    {
        return $this->model->where('role_id', 2)->get();
    }

    public function getStaff()
    {
        return $this->model->where('role_id', 3)->get();
    }

    public function search($query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%")
                          ->orWhere('phone', 'like', "%{$query}%")
                          ->paginate(15);
    }

    public function toggleStatus($id)
    {
        $user = $this->find($id);
        if ($user) {
            $user->is_active = !$user->is_active;
            $user->save();
            return $user;
        }
        return null;
    }

    public function updatePassword($id, $password)
    {
        $user = $this->find($id);
        if ($user) {
            $user->password = \Hash::make($password);
            $user->save();
            return $user;
        }
        return null;
    }

    public function getByRememberToken($token)
    {
        return $this->model->where('remember_token', $token)->first();
    }
}