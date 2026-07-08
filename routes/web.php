<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserCharacterController;

/* トップ */
Route::get('/', function () {
    return view('welcome');
});

/* ログイン・ログアウト */
Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');



/* 勤怠 */
Route::get('/dashboard', [AttendanceController::class, 'index'])->name('dashboard');

Route::post('/attendances/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('/attendances/clock-out', [AttendanceController::class, 'clockOut']);

Route::put('/attendances/{id}', [AttendanceController::class, 'update']);
Route::post('/attendances/{attendanceId}/request', [AttendanceController::class, 'storeRequest']);


/* シフト */
Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
Route::post('/shift', [ShiftController::class, 'store'])->name('shift.store');


/* 勤務表 */
Route::get('/workschedule', [WorkScheduleController::class, 'index'])->name('workschedule');


/* アカウント編集 */
Route::get('/account/edit', [UserController::class, 'edit'])->name('account.edit');
Route::put('/account/update', [UserController::class, 'update'])->name('account.update');


/*管理者*/

/* ダッシュボード */
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard');

Route::get('/admin/shifts/month', [AdminController::class, 'showShiftMonth'])
    ->name('admin.shifts.month.show');

Route::post('/admin/shifts/month/approve', [AdminController::class, 'approveShiftMonth'])
    ->name('admin.shifts.month.approve');

Route::post('/admin/shifts/month/reject', [AdminController::class, 'rejectShiftMonth'])
    ->name('admin.shifts.month.reject');

Route::post('/admin/attendance/{id}/approve', [AdminController::class, 'approveAttendance'])
    ->name('admin.attendance.approve');

Route::post('/admin/attendance/{id}/reject', [AdminController::class, 'rejectAttendance'])
    ->name('admin.attendance.reject');


// 打刻修正承認
Route::post('/admin/attendance/{id}/approve', [AdminController::class, 'approveAttendance'])
    ->name('admin.attendance.approve');

Route::post('/admin/attendance/{id}/reject', [AdminController::class, 'rejectAttendance'])
    ->name('admin.attendance.reject');

/* ユーザー管理 */
Route::get('/admin/users', [AdminUserController::class, 'index'])
    ->name('admin.users.index');

Route::post('/admin/users', [AdminUserController::class, 'store'])
    ->name('admin.users.store');

Route::get('/admin/users/{id}/edit', [AdminUserController::class, 'edit'])
    ->name('admin.users.edit');

Route::put('/admin/users/{id}', [AdminUserController::class, 'update'])
    ->name('admin.users.update');

Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])
    ->name('admin.users.destroy');

Route::delete('/admin/users/{id}/force', [AdminUserController::class, 'forceDelete'])
    ->name('admin.users.forceDelete');


/* 相棒管理 */
Route::get('/character', [UserCharacterController::class, 'index'])
    ->name('character.index');

Route::post('/character/select', [UserCharacterController::class, 'select'])
    ->name('character.select');

Route::put('/character/update', [UserCharacterController::class, 'update'])
    ->name('character.update');