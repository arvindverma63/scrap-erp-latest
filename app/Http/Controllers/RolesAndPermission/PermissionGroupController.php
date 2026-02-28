<?php

namespace App\Http\Controllers\RolesAndPermission;

use App\Http\Controllers\Controller;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

class PermissionGroupController extends Controller
{
    // Display list of all permission groups
    public function index()
    {
        $groups = PermissionGroup::all();
        return view('layouts.role.permission-groups.index', compact('groups'));
    }

    // Show form to create a new permission group
    public function create()
    {
        return view('layouts.role.permission-groups.create');
    }

    // Store new permission group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permission_groups,name',
        ]);

        PermissionGroup::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.permission-groups.index')->with('success', 'Permission group created successfully.');
    }

    // Show form to edit existing permission group
    public function edit($id)
    {
        $group = PermissionGroup::findOrFail($id);
        return view('layouts.role.permission-groups.edit', compact('group'));
    }

    // Update existing permission group
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permission_groups,name,' . $id,
        ]);

        $group = PermissionGroup::findOrFail($id);
        $group->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.permission-groups.index')->with('success', 'Permission group updated successfully.');
    }

    // Delete a permission group
    public function destroy($id)
    {
        $group = PermissionGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('admin.permission-groups.index')->with('success', 'Permission group deleted successfully.');
    }
}
