<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\RepoCat\EventController;
use App\Http\Controllers\RepoCat\RecapController;
use App\Http\Controllers\RepoCat\TilokController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\RepoCat\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\RepoCat\DelegasiController;
use App\Http\Controllers\RepoCat\EventTilokController;
use App\Http\Controllers\RepoCat\DetailEventController;
use App\Http\Controllers\RepoCat\DetailTilokController;

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
		Route::post('/document/inka/show/{id}', [DocumentController::class, 'action'])->name('document.inka.action');
	});
	
	Route::middleware(['role:admin'])->group(function(){
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

		//Route Aplikasi REPOCAT

		//EVENT ROUTE
		Route::get('/event', [EventController::class, 'index']);
		Route::post('/event/store', [EventController::class, 'store']);
		Route::get('/event/edit/{id}', [EventController::class, 'edit']);
		Route::put('/event/edit/{id}', [EventController::class, 'update']);
		Route::get('/event/destroy/{id}', [EventController::class, 'destroy']);

		//TILOK ROUTE
		Route::get('/tilok', [TilokController::class, 'index']);
		Route::post('/tilok/store', [TilokController::class, 'store']);
		Route::get('/tilok/edit/{id}', [TilokController::class, 'edit']);
		Route::put('/tilok/edit/{id}', [TilokController::class, 'update']);
		Route::get('/tilok/destroy/{id}', [TilokController::class, 'destroy']);

		//EVENT TILOK ROUTE
		Route::get('/event-tilok', [EventTilokController::class, 'index']);
		Route::get('/event-tilok/create', [EventTilokController::class, 'create']);
		Route::post('/event-tilok/store', [EventTilokController::class, 'store']);
		Route::get('/event-tilok/edit/{id}', [EventTilokController::class, 'edit']);
		Route::put('/event-tilok/edit/{id}', [EventTilokController::class, 'update']);
		Route::get('/event-tilok/destroy/{id}', [EventTilokController::class, 'destroy']);

		//REPORT ROUTE
		Route::get('/report', [ReportController::class, 'index']);
		Route::get('/report/create/{id}', [ReportController::class, 'create']);
		Route::post('/report/store', [ReportController::class, 'store']);
		Route::post('/report/upload-dokumen', [ReportController::class, 'uploadDokumen']);
		Route::get('/report/edit/{id}', [ReportController::class, 'edit']);
		Route::put('/report/edit/{id}', [ReportController::class, 'update']);
		Route::get('/report/destroy/{id}', [ReportController::class, 'destroy']);

		Route::get('/delegasi', [DelegasiController::class, 'index']);
		Route::get('/delegasi/create/{id}', [DelegasiController::class, 'create']);
		Route::post('/delegasi/store', [DelegasiController::class, 'store']);
		Route::post('/delegasi/upload-dokumen', [DelegasiController::class, 'uploadDokumen']);
		Route::get('/delegasi/edit/{id}', [DelegasiController::class, 'edit']);
		Route::put('/delegasi/edit/{id}', [DelegasiController::class, 'update']);
		Route::get('/delegasi/destroy/{id}', [DelegasiController::class, 'destroy']);

		//RECAP ROUTE
		Route::get('/recap', [RecapController::class, 'index']);
		Route::get('/recap/filter', [RecapController::class, 'index']);

		//ROUTE DETAIL EVENT
		Route::get('/detail-event', [DetailEventController::class, 'index']);
		Route::post('/detail-event/store', [DetailEventController::class, 'store']);
		Route::get('/detail-event/show/{id}', [DetailEventController::class, 'show']);

		//ROUTE DETAIL TILOK
		Route::post('/detail-tilok/store', [DetailTilokController::class, 'store']);
	});



	
});

// Route::middleware(['role:admin'])->group(function(){

// });
		
