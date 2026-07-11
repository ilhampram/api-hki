<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SwaggerController;

Route::get('/test', [SwaggerController::class, 'test']);

Route::get('/documents', [DocumentController::class, 'index']);
Route::get('/documents/search/{keyword}', [DocumentController::class, 'search']);
Route::get('/documents/status/{status}', [DocumentController::class, 'filterStatus']);
Route::get('/documents/trash', [DocumentController::class, 'trash']);
Route::get('/documents/{id}', [DocumentController::class, 'show']);
Route::get('/documents/{id}/download', [DocumentController::class, 'download']);

Route::get('/dashboard', [DocumentController::class, 'dashboard']);

Route::get(
    '/statistics/categories',
    [DocumentController::class, 'statisticsByCategory']
);

Route::middleware([
    'auth:sanctum',
    'admin'
])->group(function () {

    Route::apiResource(
        'categories',
        CategoryController::class
    );

});

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/documents', [DocumentController::class, 'store']);

    Route::put('/documents/{id}', [DocumentController::class, 'update']);

    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);

    Route::put('/documents/{id}/restore', [DocumentController::class, 'restore']);

    Route::delete('/documents/{id}/force-delete', [DocumentController::class, 'forceDelete']);

    Route::get('/documents/export/excel', [DocumentController::class, 'exportExcel']);

    Route::get('/documents/export/pdf', [DocumentController::class, 'exportPdf']);

    Route::get('/activity-log', [DocumentController::class, 'getActivityLog']);

    Route::get('/dashboard/chart', [DocumentController::class, 'dashboardChart']);

});

Route::middleware('auth:sanctum')->get('/me', function (\Illuminate\Http\Request $request) {
    return $request->user();
});