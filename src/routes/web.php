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

// 会員登録（ゲストでもログイン済みでもOK）
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ログイン（ゲストでもログイン済みでもOK）
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// ログアウト（ログイン中ユーザーのみ）
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// メール認証案内ページ（未認証ログイン済みユーザー用）
Route::get('/email/verify', fn () => view('auth.verify-email'))
    ->middleware('auth')
    ->name('verification.notice');

// メール認証リンク処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance')->with('success', 'メール認証が完了しました。');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送信
Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', '確認メールを再送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 勤怠打刻画面（表示・出勤/退勤/休憩ボタン）
Route::get('/attendance', [AttendanceController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('attendance');

// 打刻アクション（POST）
Route::post('/attendance/punch', [AttendanceController::class, 'handlePunch'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.punch');

// 勤怠一覧画面（ログイン済みユーザーの今月の勤怠記録）
Route::get('/attendance/list', [AttendanceController::class, 'showList'])
    ->middleware(['auth', 'verified'])
    ->name('attendance.list');

Route::get('/attendance/list/{year?}/{month?}', [AttendanceController::class, 'showList'])->name('attendance.list');

Route::get('/attendance/{id}', [AttendanceController::class, 'showDetail'])->name('attendance.detail');

// 修正申請保存（POST）
Route::post('/stamp_correction_request/{attendance}', [RequestController::class, 'store'])
    ->middleware('auth')
    ->name('stamp_correction.store');

// 修正申請一覧（GET）※
Route::get('/stamp_correction_request/list', [RequestController::class, 'index'])
    ->middleware('auth')
    ->name('stamp_correction.list');

Route::prefix('admin')->middleware(['web'])->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);

    Route::get('/attendance/list', [AdminAttendanceController::class, 'showDaily'])
        ->middleware('auth:admin')
        ->name('admin.attendance.list');

    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'showDetail'])
        ->middleware('auth:admin')
        ->name('admin.attendance.detail');

    Route::patch('/attendance/{id}', [AdminAttendanceController::class, 'update'])
        ->middleware('auth:admin')
        ->name('admin.attendance.update');

    Route::get('/attendance/requests/index', [RequestController::class, 'adminRequests'])
        ->name('admin.attendance.requests');
    });
