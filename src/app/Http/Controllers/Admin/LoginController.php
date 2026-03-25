<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => '管理者のみログイン可能です']);
            }

            return redirect('/admin/dashboard');
        }

        return back()->withErrors(['email' => 'ログイン情報が正しくありません']);
    }
    //
}
