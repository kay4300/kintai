<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;


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
        $user = auth()->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        return view('staff.attendance.create', compact('attendance'));
    }

    public function create()
    {
        return view('staff.attendance.create');
    }

    // 出勤
    public function startWork()
    {
        // 今日の勤怠を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // 1日1回
        if ($attendance) {
            return back();
        }
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => today(),
            'start_time' => now(),
            'status' => 1
        ]);

        return back();
    }

    // 休憩入り
    public function startBreak()
    {
        $attendance = '今日の勤怠';

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => now()
        ]);

        $attendance->update(['status' => 2]);

        return back();
    }

    // 休憩戻り
    public function endBreak()
    {
        $break = '未終了の休憩';

        $break->update([
            'end_time' => now()
        ]);

        $attendance->update(['status' => 1]);

        return back();
    }
    // 退勤
    public function endWork()
    {
        $attendance->update([
            'end_time' => now(),
            'status' => 3
        ]);

        return back()->with('message', 'お疲れ様でした');
    }
    //
}
