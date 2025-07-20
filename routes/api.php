<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DatabaseController; 

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Auth::routes();

Route::post('admin/update_schedule', [DatabaseController::class, 'updateSchedule']);

// Route::post('admin/update_schedule',function(){
//     \Log::info('ddd');
// });



