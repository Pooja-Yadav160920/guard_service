<?php

use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\User;




if (!function_exists('checkPermission')) {
    function checkPermission($permissionName = '')
{
    // Superadmin always allowed
    if (Auth::id() == 1) {
        return true;
    }

    if (!$permissionName) {
        return false;
    }

    $user = Auth::user();
    if (!$user || !$user->role) {
        return false;
    }

    // Load role with permissions
    $role = $user->role->with('permissions')->first();
    if (!$role || !$role->permissions) {
        return false;
    }

    // Convert to array if string
    $permissionNames = is_array($permissionName) ? $permissionName : [$permissionName];

    // Get all matching permissions by title
    $permissionIds = \App\Models\Permission::whereIn('title', $permissionNames)->pluck('id');

    if ($permissionIds->isEmpty()) {
        return false;
    }

    // Check if any of the permission IDs exist in user's role
    return $role->permissions->pluck('id')->intersect($permissionIds)->isNotEmpty();
}


}