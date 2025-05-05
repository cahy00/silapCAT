<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
		{
				// echo "halaman awal";
				return view('auth.login');
		}

		public function login(Request $request)
		{
				$request->validate([
					'nip' => 'required',
					'password' => 'required'
				],[
					'nip.required' => 'NIP wajib diisi',
					'password.required' => 'password wajib diisi'
				]);

				$infoLogin = [
					'nip' => $request->nip,
					'password' => $request->password
				];
				
				if(Auth::attempt($infoLogin)){
					if(Auth::user()->hasRole('admin')){
						return redirect('/dashboard');
					}elseif(Auth::user()->hasRole('operator')){
						return redirect('/event-tilok-operator');
					}elseif(Auth::user()->hasRole('pimpinan')){
						return redirect('/recap-pimpinan');
					}
					exit();
				}

				// if(Auth::attempt($infoLogin)){
				// 	$user = Auth::user();

				// 	if($user->hasRole('admin')){
				// 		return redirect(('/dashboard'));
				// 	}
				// }

				return back()->with('withErrors', 'Email atau Password Salah');
		}

		public function logout()
		{
				Auth::logout();
				return redirect('/');
		}
}
