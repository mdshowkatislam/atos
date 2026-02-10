<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DatabaseController;
use App\Http\Controllers\Backend\AccessConfigController;
use App\Http\Controllers\Backend\AccessUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Admin actions
Route::post('admin/update_schedule', [DatabaseController::class, 'updateSchedule']);


// 🔐 Attendance machine routes (LOCAL PC → LIVE SERVER)
Route::middleware('access.token')->group(function () {

    // Fetch MDB location (for caching on local PC)
    Route::get('access/config', [AccessConfigController::class, 'config']);

    // Upload MDB file from local PC
    Route::post('access/upload', [AccessUploadController::class, 'upload']);
    Route::post('access/fullSqlFileupload', [AccessUploadController::class, 'uploadFullSqlFile']);


});
