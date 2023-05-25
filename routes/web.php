<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\user\HomeController;
use App\Http\Controllers\user\InfoController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\user\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TimesheetController;
use App\Http\Controllers\user\UserReportController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\user\UserAttendanceController;
use App\Http\Controllers\AttendanceController as ControllersAttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', function () {
    dd(Auth::guard('timekeeper')->check());
});
Route::get('/', function () {
    return redirect()->route('admin.auth.login');
});


Route::group(['middleware' => 'existedloginmiddleware', 'as' => 'admin.'], function () {
    Route::group(['as' => 'auth.'], function () {
        Route::get('login', [AuthController::class, 'login'])->name('login');
        Route::post('login', [AuthController::class, 'checkLogin'])->name('check-login');
    });
});


Route::group(['prefix' => 'admin', 'middleware' => 'loginmiddleware', 'as' => 'admin.'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/dashboard/pagination', [DashboardController::class, 'pagination'])->name('dashboard.pagination');

    Route::group(['prefix' => 'staff', 'as' => 'staff.'], function () {
        Route::get('/', [StaffController::class, 'index'])->name('list');
        Route::post('/', [StaffController::class, 'pagination'])->name('pagination');
        Route::get('create', [StaffController::class, 'create'])->name('create');
        Route::post('store', [StaffController::class, 'store'])->name('store');

        Route::get('edit/{id}', [StaffController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [StaffController::class, 'update'])->name('update');

        Route::get('delete/{id}', [StaffController::class, 'delete'])->name('delete');

        Route::get('/add', [StaffController::class, 'add'])->name('add');
        Route::post('/add', [StaffController::class, 'actionAdd'])->name('p_add');

        Route::get('/add', [StaffController::class, 'add'])->name('add');
        Route::post('/add', [StaffController::class, 'actionAdd'])->name('p_add');

        Route::get('/exportcsv', [StaffController::class, 'exportCsv'])->name('exportcsv');
        Route::get('/exportpdf', [StaffController::class, 'exportPdf'])->name('exportpdf');
    });

    Route::group(['prefix' => 'attendance', 'as' => 'attendance.'], function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('list');
        Route::post('/', [AttendanceController::class, 'pagination'])->name('pagination');

        Route::get('/detail/{id?}/', [AttendanceController::class, 'detail'])->name('detail');
        Route::post('/chage-status', [AttendanceController::class, 'updateStatus'])->name('updateStatus');

        Route::get('/exportcsv', [AttendanceController::class, 'exportCsv'])->name('exportcsv');
        Route::get('/exportpdf', [AttendanceController::class, 'exportPdf'])->name('exportpdf');
    });

    Route::group(['prefix' => 'timesheet', 'as' => 'timesheet.'], function () {
        Route::get('/', [TimesheetController::class, 'index'])->name('list');
        Route::post('/', [TimesheetController::class, 'pagination'])->name('pagination');

        Route::get('/detail/{id}/attendance', [TimesheetController::class, 'attendance'])->name('attendance');
        Route::get('/detail/{id?}/', [TimesheetController::class, 'detail'])->name('detail');
    });

    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('/', [ReportController::class, 'index'])->name('list');
        Route::post('/', [ReportController::class, 'pagination'])->name('pagination');

        Route::get('/exportcsv', [ReportController::class, 'exportCsv'])->name('exportcsv');
        Route::get('/exportpdf', [ReportController::class, 'exportPdf'])->name('exportpdf');
    });
});


Route::get('/check-in/login', [ControllersAttendanceController::class, 'login'])->middleware('timekeeperloginexistedmiddleware')->name('check-in.login');
Route::post('/check-in/login', [ControllersAttendanceController::class, 'actionLogin'])->middleware('timekeeperloginexistedmiddleware')->name('check-in.plogin');
Route::group(['middleware' => 'timekeeperloginmiddleware'], function () {
    Route::get('/check-in/logout', [ControllersAttendanceController::class, 'logout'])->name('check-in.logout');
    Route::get('/check-in', [ControllersAttendanceController::class, 'index'])->name('check-in');
    Route::get('/check-in/recoginition', [ControllersAttendanceController::class, 'recognition'])->name('recoginition');
    Route::get('/recoginition/test', [ControllersAttendanceController::class, 'test'])->name('test');
    Route::post('/attendance', [ControllersAttendanceController::class, 'attendance'])->name('attendance');
    Route::post('/check-in/check-attendance', [ControllersAttendanceController::class, 'checkAttendance'])->name('check-attendance');
});


Route::group(['prefix' => 'user', 'middleware' => 'userloginmiddleware', 'as' => 'user.'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [UserAttendanceController::class, 'index'])->name('index');

    Route::group(['prefix' => 'attendance', 'as' => 'attendance.'], function () {
        Route::get('/', [UserAttendanceController::class, 'index'])->name('list');
        Route::post('/', [UserAttendanceController::class, 'pagination'])->name('pagination');

        Route::get('/detail', [UserAttendanceController::class, 'detail'])->name('detail');

        Route::post('search', [UserAttendanceController::class, 'search'])->name('search');

        Route::get('/exportcsv', [UserAttendanceController::class, 'exportCsv'])->name('exportcsv');
        Route::get('/exportpdf', [UserAttendanceController::class, 'exportPdf'])->name('exportpdf');
    });

    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('profile/reset-pass', [ProfileController::class, 'resetPass'])->name('reset-pass');

    // Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
    //     Route::get('/', [ProfileController::class, 'index'])->name('');
    //     Route::post('/', [ProfileController::class, 'pagination'])->name('pagination');
    // });

    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('/', [UserReportController::class, 'index'])->name('list');
        Route::post('/', [UserReportController::class, 'pagination'])->name('pagination');

        Route::get('create', [UserReportController::class, 'create'])->name('create');
        Route::post('store', [UserReportController::class, 'store'])->name('store');

        Route::get('edit/{id}', [UserReportController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [UserReportController::class, 'update'])->name('update');

        Route::get('delete/{id}', [UserReportController::class, 'delete'])->name('delete');
    });
});
