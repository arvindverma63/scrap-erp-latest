<?php

namespace App\Http\Controllers\RolesAndPermission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        // eager load permissions count
        $roles = Role::withCount('permissions')
            ->paginate(30);

        return view('layouts.role.roles.index', compact('roles'));
    }


    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::with('group')->get()->groupBy(function ($permission) {
            return $permission->group ? $permission->group->name : 'Ungrouped';
        });

        return view('layouts.role.roles.create', compact('permissions'));
    }


    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name, 'color' => $request->color ?? null]);

        if ($request->filled('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        // Get all permissions WITH their group relations, then group by group name or "Ungrouped"
        $permissions = \App\Models\Permission::with('group')
            ->get()
            ->groupBy(function ($permission) {
                return $permission->group ? $permission->group->name : 'Ungrouped';
            });
        // Flat array of assigned permission IDs
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('layouts.role.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }


    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array'
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name, 'color' => $request->color ?? null]);

        // Convert IDs to names before syncing permissions
        $permissionNames = Permission::whereIn('id', $request->permissions ?? [])
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($permissionNames);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }


    /**
     * Remove the specified role from storage.
     */
    public function destroy($id)
    {
        $userExists = DB::table('role_users')->where('role_id', $id)->count();
        if ($userExists == 0) {
            $role = Role::findOrFail($id);
            $role->delete();
            return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
        } else {
            return back()->with('error', 'Total ' . $userExists . ' User exist on this role');
        }
    }
}