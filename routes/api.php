<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\EventApiController;
use App\Http\Controllers\Api\v1\ExamScoreApiController;
use App\Http\Controllers\Api\v1\EmployeeApiController;
use App\Http\Controllers\Api\v1\InstitutionApiController;
use App\Http\Controllers\Api\v1\DocumentApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Legacy fallback endpoint
Route::get('/events', [EventApiController::class, 'index']);

/*
|--------------------------------------------------------------------------
| API Version 1 (V1) Endpoints for External Integrations
| Autentikasi: Header `X-API-KEY` / `Authorization: Bearer <API_KEY>` / Query `api_key`
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('/events', [EventApiController::class, 'index']);
    Route::get('/events/{id}', [EventApiController::class, 'show']);
    Route::get('/exam-scores', [ExamScoreApiController::class, 'index']);
    Route::get('/employees', [EmployeeApiController::class, 'index']);
    Route::get('/institutions', [InstitutionApiController::class, 'index']);
    Route::get('/documents', [DocumentApiController::class, 'index']);
});
