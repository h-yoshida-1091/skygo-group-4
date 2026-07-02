<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkScheduleController;

Route::get('/', function () {
    return view('welcome');
});

/*ログイン・ログアウト*/

Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

/*ダッシュボード（セッションチェック）*/

Route::get('/dashboard', [AttendanceController::class, 'index']);

/*シフト*/

Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');


// ダッシュボード表示
Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');

Route::post('/attendances/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('/attendances/clock-out', [AttendanceController::class, 'clockOut']);


/*管理者*/

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard');

Route::post('/admin/shifts/{id}/approve', [AdminController::class, 'approve'])
    ->name('admin.shifts.approve');

Route::post('/admin/shifts/{id}/reject', [AdminController::class, 'reject'])
    ->name('admin.shifts.reject');

// ポップアップからの修正アクション（PUT）
Route::put('/attendances/{id}', [AttendanceController::class, 'update']);

// 勤怠画面の表示
Route::get('/workschedule', [WorkScheduleController::class, 'index'])->name('workschedule');


