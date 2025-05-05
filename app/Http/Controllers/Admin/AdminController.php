<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
		
    public function pdsk()
    {
			return redirect('/dashboard/pdsk');
    }

		public function inka()
		{
			return redirect('/dashboard/inka');
		}

		public function admin()
		{
			return redirect('/dashboard');
		}

}
