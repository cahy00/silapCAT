<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $title = 'ROle';
        return view('user.role', compact('title'));
    }

    public function storeRole(Request $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return back()->with('success', 'Role Berhasil Diinput');
    }

    public function storePermission(Request $request)
    {
        $role = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        return back()->with('success', 'Permission Berhasil Diinput');
    }
}
