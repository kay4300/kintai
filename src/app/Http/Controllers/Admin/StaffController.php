<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffController extends Controller
{
    // スタッフ一覧
    public function index()
{
        $staffs = User::all();

        return view('admin.staff.index', compact('staffs'));
}
    // スタッフ別勤怠画面
    public function attendance(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        // 月指定（なければ今月）
        $month = $request->input('month', now()->format('Y-m'));

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [$start, $end])
            ->get();

        return view('admin.staff.attendance', compact('staff', 'attendances', 'month'));
    }

    // csvダウンロード
    public function csv(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $month = $request->input('month', now()->format('Y-m'));

        $start = \Carbon\Carbon::parse($month)->startOfMonth();
        $end = \Carbon\Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $id)
            ->whereBetween('date', [
                $start->format('Y-m-d'),
                $end->format('Y-m-d')
            ])
            ->orderBy('date')
            ->get();
        
        return response()->streamDownload(function () use ($attendances) {

            $handle = fopen('php://output', 'w');

            // ヘッダー
            fputcsv($handle, mb_convert_encoding(
                ['日付', '出勤', '退勤', '休憩時間', '合計'],
                'SJIS-win',
                'UTF-8'
            ));

            foreach ($attendances as $attendance) {
                
                $start = $attendance->start_time;
                $end = $attendance->end_time;

                // 休憩
                $breakTime = 0;
                if ($attendance->breaks) {
                    foreach ($attendance->breaks as $break) {
                        if ($break->start_time && $break->end_time) {
                            $breakTime += \Carbon\Carbon::parse($break->end_time)
                                ->diffInMinutes(\Carbon\Carbon::parse($break->start_time));
                        }
                    }
                }

                // 勤務時間
                $workMinutes = 0;
                if ($start && $end) {
                    $workMinutes = \Carbon\Carbon::parse($end)
                        ->diffInMinutes(\Carbon\Carbon::parse($start)) - $breakTime;
                }

                fputcsv($handle, mb_convert_encoding([
                    $attendance->date ?? '',
                    $start ?? '',
                    $end ?? '',
                    gmdate('H:i', $breakTime * 60),
                    gmdate('H:i', $workMinutes * 60),
                ], 'SJIS-win', 'UTF-8'));
            }

            fclose($handle);
        }, $staff->name . '_' . $month . '_attendance.csv');
    }
    //
}
