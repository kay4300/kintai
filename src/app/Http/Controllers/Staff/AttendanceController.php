<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;
use Carbon\CarbonImmutable;

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
            return back()->with('error', '本日の勤務は終了しています');
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
    // 勤怠一覧
    public function list(Request $request)
    {
        Carbon::setLocale('ja');

        // 1. 対象月取得
        $month = $request->input('month');

        if (!$month) {
            $month = now()->format('Y-m');
        }
        $date = CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
        // 2. 前月・翌月リンク用
        $prevMonth = $date->copy()->subMonth()->format('Y-m');
        $nextMonth = $date->copy()->addMonth()->format('Y-m');
        $currentMonth = $date->format('Y年m月');

        // 3. 月初〜月末
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        // 4. DBから勤怠取得（Breakリレーション付き）
        $attendances = Attendance::where('user_id', auth()->id())
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->with('breaks')
            ->get()
            ->keyBy(function ($item) {
                return \Carbon\Carbon::parse($item->date)->toDateString();
            });

        // 5. 月の全日付カレンダー作成
        $calendar = [];
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $day) {
            $key = $day->format('Y-m-d');

            $attendance = $attendances[$key] ?? null;

            // 休憩合計（分）
            $totalBreak = $attendance
                ? $attendance->breakTimes->sum(function ($break) {
                    return \Carbon\Carbon::parse($break->end_time)
                        ->diffInMinutes(\Carbon\Carbon::parse($break->start_time));
                })
                : 0;
            // $totalBreak = $attendance ? $attendance->breaks->sum('duration') : 0;

            // 実働時間（分）
            $workMinutes = $attendance
                ? Carbon::parse($attendance->start_time)->diffInMinutes(Carbon::parse($attendance->end_time)) - $totalBreak
                : 0;

            $calendar[] = [
                'date' => $key,
                'attendance' => $attendance,
                'total_break' => $totalBreak,
                'work_minutes' => $workMinutes,
            ];
        }

        return view('staff.attendance.index', compact(
            'calendar',
            'currentMonth',
            'prevMonth',
            'nextMonth'
        ));
    }
    // 詳細画面
    public function detail($id, $requestId = null)
    {
        $attendance = Attendance::with('breaks')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $requestData = null;

        // ① 申請経由で来た場合
        if ($requestId) {
            $requestData = StampCorrectionRequest::where('id', $requestId)
                ->where('attendance_id', $attendance->id)
                ->firstOrFail();
        } else {
            // 勤怠一覧から来た場合でも申請を探す
            $requestData = StampCorrectionRequest::where('attendance_id', $attendance->id)
                ->latest()
                ->first();
        }
        // 承認待ちかどうか
        $isPending = !is_null($requestData);

        return view('staff.attendance.detail', compact('attendance', 'isPending', 'requestData'));
    }
    // 勤怠修正
    public function update(Request $request, $id)
    {
        $attendance = Attendance::with('breaks')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $date = \Carbon\Carbon::parse($attendance->date)->format('Y-m-d');

        // 出勤・退勤更新
        $attendance->update([
            'start_time' => $request->start_time
                ? Carbon::parse($date . ' ' . $request->start_time)
                : null,

            'end_time' => $request->end_time
                ? Carbon::parse($date . ' ' . $request->end_time)
                : null,
            'note' => $request->note,
        ]);

        $breaks = $attendance->breaks;

        // 休憩1
        if (isset($breaks[0])) {
            $breaks[0]->update([
                'start_time' => $request->break_start_1
                    ? Carbon::parse($date . ' ' . $request->break_start_1)
                    : null,

                'end_time' => $request->break_end_1
                    ? Carbon::parse($date . ' ' . $request->break_end_1)
                    : null,
            ]);
        }

        // 休憩2
        if (isset($breaks[1])) {
            $breaks[1]->update([
                'start_time' => $request->break_start_2
                    ? Carbon::parse($date . ' ' . $request->break_start_2)
                    : null,

                'end_time' => $request->break_end_2
                    ? Carbon::parse($date . ' ' . $request->break_end_2)
                    : null,
            ]);
        }

        return redirect()->back()->with('message', '更新しました');
    }

    // ログアウト
    public function logout(Request $request)
    {
        Auth::logout(); // ★ログイン状態を解除

        $request->session()->invalidate(); // ★セッション破棄
        $request->session()->regenerateToken(); // ★CSRF再生成

        return redirect('/login');
    }
    //
}
