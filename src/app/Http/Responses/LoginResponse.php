<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // $user = Auth::user();
        $user = $request->user();

        // ここで null チェック
        if (!$user) {
            return redirect()->route('login');
        }

        // メール認証済みかどうか
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('staff.attendance.index'); // 認証済みなら出勤ページ
        }

        // 未認証ならメール認証ページ
        return redirect()->route('verification.notice');
    }

    // if ($user->hasVerifiedEmail()) {

    //     return redirect()->route('attendance');
    // }

    // return redirect()->route('verification.notice');
    // return redirect()->intended('/dashboard');
    // 
}
