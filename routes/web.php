<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;


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