<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
           return view('welcome');
});
 
Auth::routes();

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'postLogin'])->name('post.login');
// Route::get('/verify-token', [LoginController::class,'showVerifyTokenForm'])->name('verify.token');
// Route::post('/verify-token', [LoginController::class,'postVerifyToken'])->name('post.verify.token');
Route::post('/logout', [LoginController::class,'logOut'])->name('logout');


Route::middleware(['auth'])->group(function () {


Route::get('/home', [HomeController::class, 'index'])->name('dashboard');
Route::get('/home1', [HomeController::class, 'index'])->name('index');

Route::post('/access/upload', [AccessController::class, 'upload'])->name('access.upload');
Route::get('/access/tables', [AccessController::class, 'listTables'])->name('access.tables');
Route::get('/access/sql/{table}', [AccessController::class, 'convertToSQL'])->name('access.sql');
Route::get('/access/table/{table}', [AccessController::class, 'showTable'])->name('access.showTable');
});

  Route::prefix('user')->group(function () {
            Route::get('/', 'UserController@index')->name('user');
            Route::get('/add', 'UserController@userAdd')->name('user.add');
            Route::post('/store', 'UserController@userStore')->name('user.store');
            Route::get('/edit/{id}', 'UserController@userEdit')->name('user.edit');
            Route::post('/update/{id}', 'UserController@updateUser')->name('user.update');
            Route::post('/delete', 'UserController@deleteUser')->name('user.delete');
           
            Route::get('/user/status/', 'UserController@userStatus')->name('user.status.change');

            Route::get('/role', 'Backend\Menu\RoleController@index')->name('user.role');
            Route::post('/role/store', 'Backend\Menu\RoleController@storeRole')->name('user.role.store');
            Route::get('/role/edit', 'Backend\Menu\RoleController@getRole')->name('user.role.edit');
            Route::post('/role/update/{id}', 'Backend\Menu\RoleController@updateRole')->name('user.role.update');
            Route::post('/role/delete', 'Backend\Menu\RoleController@deleteRole')->name('user.role.delete');

          
          
        });