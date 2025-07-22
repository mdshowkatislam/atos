<?php
use Illuminate\Http\Request; 
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\Backend\DatabaseController;
use App\Http\Controllers\Backend\ShitController;
use App\Http\Controllers\Backend\AttendanceModule\ShiftController;
use App\Http\Controllers\Backend\AttendanceModule\flaxibleTimeController;
use App\Http\Controllers\Backend\AttendanceModule\GroupController;
use App\Http\Controllers\Backend\AttendanceModule\SpecialWorkdayController;
use App\Jobs\PushSelectedColumn;


Route::post('/access/upload', [AccessController::class, 'upload'])->name('access.upload');
Route::get('/access/tables', [AccessController::class, 'listTables'])->name('access.tables');
Route::get('/access/sql/{table}', [AccessController::class, 'convertToSQL'])->name('access.sql');
Route::get('/access/table/{table}', [AccessController::class, 'showTable'])->name('access.showTable');

Route::get('/', function() {
    return view('welcome');
})->name('welcome');

Auth::routes();
Route::get('/home', [App\Http\Controllers\Backend\HomeController::class, 'index'])->name('home');
// User Management
Route::prefix('admin')->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('user.users');       
            Route::get('/add', [UserController::class,'userAdd'])->name('user.add');
            Route::post('/store', [UserController::class,'userStore'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class,'userEdit'])->name('user.edit');
            Route::post('/update/{id}', [UserController::class,'updateUser'])->name('user.update');
            Route::post('/delete', [UserController::class,'deleteUser'])->name('user.delete');
            Route::get('/reset-password', [UserController::class,'resetPassword'])->name('reset.password');
});


//  Database Management 👈

Route::get('admin/database_management', [DatabaseController::class, 'index'])->name('admin.database_management');
Route::post('admin/update_schedule', [DatabaseController::class, 'updateSchedule'])->name('admin.update_schedule');

// Table Management
Route::get('admin/table_management', [DatabaseController::class, 'showTable'])->name('admin.table_management');
Route::get('admin/table/column/{table}', [DatabaseController::class, 'showColumn'])->name('admin.table.column');

Route::get('admin/table/selected-columns', [DatabaseController::class, 'showSelected'])->name('admin.table.showSelected');

// important route for 2nd job
// Route::post('admin/table/send', function (Request $request) { 

//     $table   = $request->string('table'); 
//     $columns = $request->array('columns');  
//     // $columns = $request->input('columns', []);   
    
//     // optional: store the request, show a toast, whatever…

//     PushSelectedColumn::dispatch($table, $columns);

//     // return response()->json(['queued' => true]);
//     return back()->with('queued', true);
// })->name('admin.table.send');


// ===============   Attendance Management System (api based)   =================

// Route::get('admin/shift_manage', [ShiftController::class, 'shiftManage'])->name('admin.shift_manage');
// Route::post('admin/shift/add', [ShiftController::class, 'shiftAdd'])->name('admin.sh
// ift.add');

Route::prefix('admin/shift_manage')->group(function () {
    Route::get('/', [ShiftController::class, 'index'])->name('shift.list');
    Route::get('/add', [ShiftController::class, 'create'])->name('shift.add');
    Route::post('/store', [ShiftController::class, 'store'])->name('shift.store');
    Route::get('/{id}/edit', [ShiftController::class, 'edit'])->name('shift.edit');
    Route::put('/{id}', [ShiftController::class, 'update'])->name('shift.update');
    Route::delete('/{id}', [ShiftController::class, 'destroy'])->name('shift.destroy');
});
Route::prefix('admin/group_manage')->group(function () {
    Route::get('/', [GroupController::class, 'index'])->name('group.list');
    Route::get('/add', [GroupController::class, 'create'])->name('group.add');
    Route::post('/store', [GroupController::class, 'store'])->name('group.store');
    Route::get('/{id}/edit', [GroupController::class, 'edit'])->name('group.edit');
    Route::put('/{id}', [GroupController::class, 'update'])->name('group.update');
    Route::delete('/{id}', [GroupController::class, 'destroy'])->name('group.destroy');
});
