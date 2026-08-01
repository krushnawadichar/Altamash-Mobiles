<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Services\UserService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->roleService->getAllRoles();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $this->userService->createUser($request->validated());
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = $this->userService->getUserById($id);
        $roles = $this->roleService->getAllRoles();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, $id)
    {
        $this->userService->updateUser($id, $request->validated());
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $this->userService->toggleStatus($id);
        return redirect()->route('admin.users.index')
            ->with('success', 'User status updated successfully.');
    }
}