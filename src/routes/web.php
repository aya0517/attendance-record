<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AdminStaffController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ユーザー認証
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// メール認証
Route::get('/email/verify', fn () => view('auth.verify-email'))->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance')->with('success', 'メール認証が完了しました。');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', '確認メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 勤怠打刻・一覧・詳細
Route::get('/attendance', [AttendanceController::class, 'index'])->middleware(['auth', 'verified'])->name('attendance');
Route::post('/attendance/punch', [AttendanceController::class, 'handlePunch'])->middleware(['auth', 'verified'])->name('attendance.punch');
Route::get('/attendance/list', [AttendanceController::class, 'showList'])->middleware(['auth', 'verified'])->name('attendance.list');
Route::patch('/attendance/{id}', [AttendanceController::class, 'update'])->middleware('auth')->name('attendance.update');
Route::get('/attendance/{id}', [AttendanceController::class, 'showDetail'])->name('attendance.detail');

// 勤怠修正申請
Route::post('/stamp_correction_request/{attendance}', [RequestController::class, 'store'])->middleware('auth')->name('stamp_correction.store');
Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])->middleware('auth')->name('stamp_correction.list');

// 管理者
Route::prefix('admin')->group(function () {

    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);

    Route::middleware(['auth:admin'])->group(function () {

        // 勤怠
        Route::get('/attendance/list', [AdminAttendanceController::class, 'showDaily'])->name('admin.attendance.list');
        Route::get('/attendance/{id}', [AdminAttendanceController::class, 'showDetail'])->name('admin.attendance.detail');
        Route::patch('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

        // 勤怠修正申請
        Route::get('/attendance/requests/index', [AdminRequestController::class, 'index'])->name('admin.attendance.requests');
        Route::post('/attendance/requests/approve/{id}', [AdminRequestController::class, 'approve'])->name('admin.attendance.approve');
        Route::get('/attendance/requests/{request}', [AdminRequestController::class, 'showApprove'])->middleware('auth:admin')->name('admin.requests_approve');

        // スタッフ管理
        Route::get('/staffs', [AdminStaffController::class, 'index'])->name('admin.staffs.index');
        Route::get('/staffs/{id}', [AdminStaffController::class, 'showDetail'])->name('admin.staffs.detail');
        Route::get('/staffs/{id}/export', [AdminStaffController::class, 'export'])->name('admin.staffs.export');
    });
});

// 管理者ログアウト
Route::post('/admin/logout', function () {
    Auth::guard('admin')->logout();
    return redirect('/admin/login');
})->middleware('auth:admin')->name('admin.logout');
