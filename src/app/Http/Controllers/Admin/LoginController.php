<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminLoginRequest;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // ログイン処理
    public function login(AdminLoginRequest $request)
    {
        // FormRequestでバリデーション済み
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が正しくありません',
        ]);
    }

    // 勤怠一覧
    // public function dashboard()
    // {
        // 後でAttendanceモデルに差し替え
    //     $attendances = [];

    //     return view('admin.dashboard', compact('attendances'));
    // }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
    //
}
