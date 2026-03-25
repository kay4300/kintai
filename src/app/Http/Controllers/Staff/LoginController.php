<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StaffLoginRequest;

class LoginController extends Controller
{
    public function login(StaffLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        // メールとパスワードが正しいか
        if (Auth::attempt($credentials)) {
            
            // ログイン成功したユーザーを取得
            $user = Auth::user();
            // スタッフかどうか確認
            if ($user->role !== 'staff') {
                Auth::logout();
                return back()->withErrors(['email' => '権限がありません']);
            }

            return redirect('/staff/dashboard');
        }

        return back()->withErrors(['email' => 'ログイン情報が正しくありません']);
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
        ]);

        Auth::login($user);

        return redirect('/staff/dashboard');
    }

    //
}
