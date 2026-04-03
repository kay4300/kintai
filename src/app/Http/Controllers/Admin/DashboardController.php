<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // monthパラメータがあれば使う、なければ今日
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        // 表示用
        $currentDate = $date->format('Y年n月j日');
        $currentMonth = $date->format('Y年n月');

        // 前日・翌日
        $prevDate = $date->copy()->subDay()->format('Y-m-d');
        $nextDate = $date->copy()->addDay()->format('Y-m-d');

        $attendances = Attendance::with(['user', 'breakTimes'])
            ->whereDate('date', $date)
            ->get();

        return view('admin.dashboard', compact(
            'currentDate',
            'currentMonth',
            'prevDate',
            'nextDate',
            'attendances'
        ));
    }

    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);

        return view('admin.attendance.detail', compact('attendance'));
    }
         //
}
