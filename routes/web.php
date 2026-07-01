<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return view('welcome');
});

//ログイン処理
Route::get('/login', [UserController::class, 'showLoginForm']);
Route::post('/login', [UserController::class, 'login']);

//ログアウト処理
Route::post('/logout', [UserController::class, 'logout']);

//ダッシュボード
Route::get('/dashboard', [UserController::class, 'dashboard']);

//シフト関連
Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');

// ダッシュボード表示
Route::get('/dashboard', [AttendanceController::class, 'index']);

// 打刻アクション
Route::post('/attendances/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('/attendances/clock-out', [AttendanceController::class, 'clockOut']);

// ポップアップからの修正アクション（PUT）
Route::put('/attendances/{id}', [AttendanceController::class, 'update']);