<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getWithRole();
    }

    public function getUserById($id)
    {
        return $this->userRepository->find($id);
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->getByEmail($email);
    }

    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['created_by'] = auth()->id();

        if (isset($data['avatar']) && $data['avatar']) {
            $data['avatar'] = $this->uploadAvatar($data['avatar']);
        }

        return $this->userRepository->create($data);
    }

    public function updateUser($id, array $data)
    {
        $user = $this->userRepository->find($id);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['avatar']) && $data['avatar']) {
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }
            $data['avatar'] = $this->uploadAvatar($data['avatar']);
        }

        return $this->userRepository->update($user, $data);
    }

    public function deleteUser($id)
    {
        $user = $this->userRepository->find($id);
        if ($user->avatar) {
            Storage::delete('public/' . $user->avatar);
        }
        return $this->userRepository->delete($user);
    }

    public function toggleStatus($id)
    {
        return $this->userRepository->toggleStatus($id);
    }

    public function updatePassword($id, $password)
    {
        return $this->userRepository->updatePassword($id, $password);
    }

    protected function uploadAvatar($avatar)
    {
        $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
        return $avatar->storeAs('users/avatars', $filename, 'public');
    }

    public function getUsersByRole($roleId)
    {
        return $this->userRepository->getByRole($roleId);
    }

    public function getAdmins()
    {
        return $this->userRepository->getAdmins();
    }

    public function getManagers()
    {
        return $this->userRepository->getManagers();
    }

    public function getStaff()
    {
        return $this->userRepository->getStaff();
    }

    public function getActiveUsers()
    {
        return $this->userRepository->getActiveWithRole();
    }

    public function searchUsers($query)
    {
        return $this->userRepository->search($query);
    }

    public function getTotalUsers()
    {
        return $this->userRepository->count();
    }

    public function getActiveUserCount()
    {
        return $this->userRepository->where('is_active', true)->count();
    }

    public function getInactiveUserCount()
    {
        return $this->userRepository->where('is_active', false)->count();
    }

    public function getUserStatistics()
    {
        $total = $this->userRepository->count();
        $active = $this->userRepository->where('is_active', true)->count();
        $inactive = $this->userRepository->where('is_active', false)->count();
        $admins = $this->userRepository->getAdmins()->count();
        $managers = $this->userRepository->getManagers()->count();
        $staff = $this->userRepository->getStaff()->count();

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'admins' => $admins,
            'managers' => $managers,
            'staff' => $staff,
            'active_percentage' => $total > 0 ? round(($active / $total) * 100, 2) : 0,
        ];
    }
}