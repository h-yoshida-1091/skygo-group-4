
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\AdminUserController;

/*トップ*/

Route::get('/', function () {
    return view('welcome');
});

/*ログイン・ログアウト*/
Route::get('/login', [UserController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [UserController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [UserController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*ユーザー（認証必須）*/
Route::middleware('auth')->group(function () {

    // ダッシュボード
    Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');

    // 勤怠
    Route::post('/attendances/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendances/clock-out', [AttendanceController::class, 'clockOut']);

    // シフト
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');


    // 勤怠修正
    Route::put('/attendances/{id}', [AttendanceController::class, 'update']);

    // ダッシュボード表示
    Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/{attendance_id}/request', [AttendanceController::class, 'storeRequest']);

    // 勤務表
    Route::get('/workschedule', [WorkScheduleController::class, 'index'])->name('workschedule');

    // アカウント編集
    Route::get('/account/edit', [UserController::class, 'edit'])->name('account.edit');
    Route::put('/account/update', [UserController::class, 'update'])->name('account.update');
});

/*管理者（認証必須）*/
Route::middleware('auth')->prefix('admin')->group(function () {

    // 管理ダッシュボード
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // シフト承認
    Route::post('/shifts/{id}/approve', [AdminController::class, 'approve'])
        ->name('admin.shifts.approve');

    Route::post('/shifts/{id}/reject', [AdminController::class, 'reject'])
        ->name('admin.shifts.reject');

    // ユーザー管理
    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');

    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])
        ->name('admin.users.destroy');

    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('/users/{id}', [AdminUserController::class, 'update'])
        ->name('admin.users.update');
});

