<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\Backend\DatabaseListController;

 

Route::post('/access/upload', [AccessController::class, 'upload'])->name('access.upload');
Route::get('/access/tables', [AccessController::class, 'listTables'])->name('access.tables');
Route::get('/access/sql/{table}', [AccessController::class, 'convertToSQL'])->name('access.sql');
Route::get('/access/table/{table}', [AccessController::class, 'showTable'])->name('access.showTable');



Auth::routes();



Route::get('/home', [App\Http\Controllers\Backend\HomeController::class, 'index'])->name('home');

Route::get('admin/settings/databases', function () {
    return view('show_all_db');
});
Route::get('admin/db_management/db_list', function () {
    return view('show_all_db');
});
Route::get('admin/db_management/db_list', [DatabaseListController::class, 'index'])->name('db_management.db_list');

Route::get('admin/user_management/users', [UserController::class, 'index'])->name('user_management.users');

Route::prefix('user')->group(function () {
            Route::get('/', 'UserController@index')->name('user');
         
            Route::get('/add', 'UserController@userAdd')->name('user.add');
            Route::post('/store', 'UserController@userStore')->name('user.store');
            Route::get('/edit/{id}', 'UserController@userEdit')->name('user.edit');
            Route::post('/update/{id}', 'UserController@updateUser')->name('user.update');
            Route::post('/delete', 'UserController@deleteUser')->name('user.delete');
            Route::get('/reset-password', 'UserController@resetPassword')->name('reset.password');
});