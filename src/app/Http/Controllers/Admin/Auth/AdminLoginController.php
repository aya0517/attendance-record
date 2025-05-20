<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    Log::info('認証を試みます', $credentials);

    if (Auth::guard('admin')->attempt($credentials)) {
        Log::info('ログイン成功', ['admin_id' => Auth::guard('admin')->id()]);
        return redirect()->intended('/admin/attendance/list');
    }

    Log::warning('ログイン失敗', ['credentials' => $credentials]);

    return back()->withErrors([
        'email' => 'ログイン情報が登録されていません。'
    ])->withInput();
}


    public function logout()
        {
            Auth::logout();
            return redirect()->route('admin/login');
        }
}
