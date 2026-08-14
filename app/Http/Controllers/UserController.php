<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'location', 'warehouse')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        $locations = Location::pluck('name', 'id')->all();
        $warehouses = \App\Models\Warehouse::pluck('name', 'id')->all();
        return view('users.create', compact('roles', 'locations', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'required|array',
            'location_id' => 'required|exists:locations,id',
            'warehouse_id' => 'nullable|exists:warehouses,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'location_id' => $request->location_id,
            'warehouse_id' => $request->warehouse_id,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRoles = $user->roles->pluck('name', 'name')->all();
        $locations = Location::pluck('name', 'id')->all();
        $warehouses = \App\Models\Warehouse::pluck('name', 'id')->all();
        return view('users.edit', compact('user', 'roles', 'userRoles', 'locations', 'warehouses'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'roles' => 'required|array',
            'location_id' => 'required|exists:locations,id',
            'warehouse_id' => 'nullable|exists:warehouses,id'
        ]);

        $data = $request->only(['name', 'email', 'location_id', 'warehouse_id']);
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        if(!empty($request->password)){
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
