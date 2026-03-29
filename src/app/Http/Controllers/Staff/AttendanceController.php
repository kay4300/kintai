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
        $user = auth()->user();

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
        $user = auth()->user();

        // 今日の勤怠を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // 勤怠が存在しない場合の保険
        if (!$attendance) {
            return back()->with('error', '先に出勤してください');
        }

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
        $user = auth()->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('end_time')
            ->first();

        if ($break) {
            $break->update([
                'end_time' => now()
            ]);
        }

        if ($attendance) {
            $attendance->update(['status' => 1]);
        }

        return back();
    }
    // 退勤
    public function endWork()
    {
        $user = auth()->user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if ($attendance) {
            $attendance->update([
                'end_time' => now(),
                'status' => 3
            ]);
        }

        return back()->with('message', 'お疲れ様でした');
    }

    public function list()
    {
        $attendances = Attendance::where('user_id', auth()->id())->get();

        return view('staff.attendance.index', compact('attendances'));
    }
    //
}
