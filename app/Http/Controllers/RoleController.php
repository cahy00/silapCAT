<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $title = 'ROle';
        return view('user.role', compact('title'));
    }

    public function storeRole(Request $request)
    {
        // $request->validate([
        //     ''
        // ])
    }
}
