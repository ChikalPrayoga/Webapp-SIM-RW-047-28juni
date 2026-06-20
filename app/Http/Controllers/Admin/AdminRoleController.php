<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    public function index()
    {
        // Get roles with user counts
        $roles = Role::withCount('users')->orderBy('role_name')->get();
        return view('admin.roles.index', compact('roles'));
    }
}
