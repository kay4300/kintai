<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;

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
    // 修正表示
    public function show($id)
    {
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
    
        // 修正申請があるかどうか
        // $requestData = $attendance->request ?? null;
        $requestData = StampCorrectionRequest::where('attendance_id', $attendance->id)
            // ->where('status', 0)
            ->latest()
            ->first();

        // 申請があれば編集不可
        $isPending = !is_null($requestData);
        // $isLocked = !is_null($requestData);
        // $isPending = $requestData && $requestData->status == 0;
        
        return view('admin.attendance.detail', compact('attendance', 'isPending', 'requestData'));
    }
    // 保存
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        // $exists = StampCorrectionRequest::where('attendance_id', $attendance->id)
        //     ->exists();

        if (StampCorrectionRequest::where('attendance_id', $attendance->id)->exists()) {
            abort(403, 'この勤怠は修正できません');
        }    
        // if ($exists) {
        //     return back()->with('error', 'すでに申請があります');
        // }

        StampCorrectionRequest::create([
            'user_id' => $attendance->user_id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date ?? now(), // カラムに合わせて調整
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'status' => 0, // 承認待ち
        ]);

        return back()->with('success', '更新しました');
    }
    // 承認画面表示
    public function approve($id)
    {
        $requestData = StampCorrectionRequest::with('attendance.user', 'attendance.breakTimes')
            ->findOrFail($id);

        $attendance = $requestData->attendance;

        $isPending = true;

        $isApproveMode = true;

        return view('shared.attendance_detail', compact(
            'attendance',
            'requestData',
            'isApproveMode'
        ));
    }
    // 承認処理
    public function approveUpdate($id)
    {
        $requestData = StampCorrectionRequest::with('attendance')->findOrFail($id);
    
        // すでに承認済みなら何もしない
        if ($requestData->status == 1) {
            return back();
        }

        $attendance = $requestData->attendance;
        
        // 勤怠に反映
        if ($requestData->start_time) {
            $attendance->start_time = $requestData->start_time;
        }

        if ($requestData->end_time) {
            $attendance->end_time = $requestData->end_time;
        }
        
        $attendance->save();

        // 承認済みに変更
        $requestData->status = 1;
        $requestData->save();

        return redirect()->route('admin.stamp_correction_request.approve', $id);
    }
         //
}
