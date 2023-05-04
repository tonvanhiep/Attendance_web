<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'ApiCheckLogin'])->name('apiLogin');

Route::group(['middleware' => 'auth:api', 'as'=> 'admin.'], function ()
{
    Route::get('/logout', [AuthController::class, 'ApiLogout'])->name('logout');


    Route::get('/user', [DashboardController::class, 'ApiGetUser']);
    Route::get('/info', [AuthController::class, 'ApiGetInfo']);


    Route::get('/attendance', [AttendanceController::class, 'ApiGetAttendance'])->name('logout');
});

// Route::group(['prefix' => 'staff', 'as'=> 'staff.'], function () {
//     Route::get('/exportcsv', [StaffController::class, 'exportCsv'])->name('exportcsv');
//     Route::get('/exportpdf', [StaffController::class, 'exportPdf'])->name('exportpdf');
// });
