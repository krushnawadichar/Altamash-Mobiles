<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|unique:users,username',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:super_admin,admin,salesman,technician',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'active',
            'password' => Hash::make($request->password),
        ]);

        $roleModel = Role::where('slug', $request->role)->first();
        if ($roleModel) {
            $user->roles()->sync([$roleModel->id]);
        }

        ActivityLog::log('USER_CREATED', 'users', $user->id, "Created user {$user->name} ({$user->role})");

        return back()->with('success', 'Staff member created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|in:super_admin,admin,salesman,technician',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|min:6',
        ]);

        $data = $request->only(['name', 'email', 'username', 'phone', 'role', 'status']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $roleModel = Role::where('slug', $request->role)->first();
        if ($roleModel) {
            $user->roles()->sync([$roleModel->id]);
        }

        ActivityLog::log('USER_UPDATED', 'users', $user->id, "Updated staff member {$user->name}");

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();
        ActivityLog::log('USER_DELETED', 'users', null, "Deleted user {$name}");

        return back()->with('success', 'User deleted successfully.');
    }
}
