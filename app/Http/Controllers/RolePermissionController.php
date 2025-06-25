<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getUserPermissions(Request $request)
    {
        
    $user = auth()->user(); // ya $request->user()
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
        
        if($user->id == 1){
        $permissions = Permission::get();
        } else {
            $assignedPermissions = RolePermission::where('role_id', $user->role->id)->pluck('permission_id');
        $permissions = Permission::whereIn('id', $assignedPermissions)->get();
        }

        return response()->json([
            'permissions' => $permissions
        ]);
    }

    // Get all roles and permissions
    public function metadata()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    // Assign permissions to role
    public function store(Request $request)
    {
        if (!checkPermission('add-permission')) {
            return response()->json(['error' => 'Permission denied!'], 403);
        }

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        RolePermission::where('role_id', $request->role_id)->delete();

        foreach ($request->permissions as $permission_id) {
            RolePermission::create([
                'role_id' => $request->role_id,
                'permission_id' => $permission_id
            ]);
        }

        return response()->json(['message' => 'Permissions assigned to role successfully.']);
    }

    // Get permissions of a specific role
    public function show($role_id)
    {
        if (!checkPermission('edit-permission')) {
            return response()->json(['error' => 'Permission denied!'], 403);
        }

        $role = Role::findOrFail($role_id);
        $permissions = Permission::all();
        $assignedPermissions = RolePermission::where('role_id', $role_id)->pluck('permission_id');

        return response()->json([
            'role' => $role,
            'permissions' => $permissions,
            'assigned_permissions' => $assignedPermissions
        ]);
    }

    // Update role's permissions
    public function update(Request $request, $role_id)
    {
        if (!checkPermission('edit-permission')) {
            return response()->json(['error' => 'Permission denied!'], 403);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        RolePermission::where('role_id', $role_id)->delete();

        foreach ($request->permissions as $permission_id) {
            RolePermission::create([
                'role_id' => $role_id,
                'permission_id' => $permission_id
            ]);
        }

        return response()->json(['message' => 'Permissions updated successfully.']);
    }
}
