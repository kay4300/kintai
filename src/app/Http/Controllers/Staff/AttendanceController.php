<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // ログインユーザー専用にする場合は auth ミドルウェアを追加
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 出勤管理画面
    public function index()
    {
        // ここでユーザーや出勤データを取得してビューに渡せます
        return view('staff.attendance');
    }
    //
}
