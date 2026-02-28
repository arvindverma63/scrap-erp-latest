<?php

namespace App\Http\Controllers\RolesAndPermission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    // Display list of all permissions
    public function index()
    {
        // Load permissions with group relation
        $permissions = Permission::with('group')->get();

        // Group by group name, fallback to 'Ungrouped' if missing
        $groupedPermissions = $permissions->groupBy(function ($item) {
            return $item->group ? $item->group->name : 'Ungrouped';
        });

        return view('layouts.role.permissions.index', ['permissions' => $groupedPermissions]);
    }

    // Show the create form
    public function create()
    {
        $groups = PermissionGroup::all();
        return view('layouts.role.permissions.create', compact('groups'));
    }


    // Store new permission
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'group_id' => 'required|exists:permission_groups,id',
        ]);

        Permission::create([
            'name' => $request->name,
            'group_id' => $request->group_id,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    // Show the edit form
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $groups = PermissionGroup::all();
        return view('layouts.role.permissions.edit', compact('permission', 'groups'));
    }

    // Update an existing permission
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'group_id' => 'required|exists:permission_groups,id',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
            'group_id' => $request->group_id,
        ]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    // Delete a permission
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
