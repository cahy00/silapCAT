<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['guest'])->group(function(){
	
});
Route::get('/home', function(){
	return redirect('/dashboard');
});
Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login']);

Route::middleware(['auth'])->group(function(){
	Route::get('/logout', [LoginController::class, 'logout']);

	Route::middleware(['userAkses:pdsk'])->group(function(){
		Route::get('/pdsk', [AdminController::class, 'pdsk']);
		Route::get('/document/pdsk/{id}', [DocumentController::class, 'index']);
		Route::get('/dashboard/pdsk', [DashboardController::class, 'index']);
	});

	Route::middleware(['userAkses:inka'])->group(function(){
		Route::get('/inka', [AdminController::class, 'inka']);
		Route::get('/dashboard/inka', [DashboardController::class, 'index']);
		Route::post('/document/inka', [DocumentController::class, 'store']);
		Route::get('/document/inka/{id}', [DocumentController::class, 'index'])->name('document.inka');
		Route::get('/document/inka/show/{id}', [DocumentController::class, 'show'])->name('document.inka.show');
	});
	
	Route::middleware(['userAkses:admin'])->group(function(){
		Route::get('/dashboard', [DashboardController::class, 'index']);
		Route::get('/user', [UserController::class, 'index']);
		Route::post('/user', [UserController::class, 'store']);
		Route::post('/type', [TypeController::class, 'store']);
		Route::get('/type', [TypeController::class, 'index']);
		Route::post('/document', [DocumentController::class, 'store']);
		Route::get('/document/{id}', [DocumentController::class, 'index'])->name('document');
		Route::get('/document/show/{id}', [DocumentController::class, 'show'])->name('document.show');
		Route::get('/document/edit/{id}', [DocumentController::class, 'edit']);
		Route::put('/document/edit/{id}', [DocumentController::class, 'update']);
		Route::get('/document/destroy/{id}', [DocumentController::class, 'destroy']);
		// Route::post('/document/inka', [DocumentController::class, 'store']);
	});
});
