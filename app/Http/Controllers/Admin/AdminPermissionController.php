<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class AdminPermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('role_name')->get();
        // Extract distinct permissions from all roles for the matrix header
        $allPermissions = Permission::orderBy('permission_name')->pluck('permission_name')->unique();
        
        return view('admin.permissions.index', compact('roles', 'allPermissions'));
    }
}
