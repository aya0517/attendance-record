<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


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

// 勤怠画面（ログイン + メール認証済み）
Route::get('/attendance', [AuthController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('attendance');