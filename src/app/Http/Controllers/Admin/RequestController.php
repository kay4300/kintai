<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;


class RequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = StampCorrectionRequest::with('user');

        if ($status === 'pending') {
            $query->where('status', 0);
        } elseif ($status === 'approved') {
            $query->where('status', 1);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return view('admin.request.index', compact('requests', 'status'));
    }

    public function approve($id)
    {
        $requestData = StampCorrectionRequest::with('attendance.breakTimes')->findOrFail($id);

        // すでに承認済みなら何もしない
        if ($requestData->status == 1) {
            return back();
        }

        $attendance = $requestData->attendance;

        // =====================
        // 勤怠反映（datetime化）
        // =====================
        $date = Carbon::parse($attendance->date)->format('Y-m-d');

        $start = $requestData->start_time
            ? Carbon::parse($date . ' ' . $requestData->start_time)
            : null;

        $end = $requestData->end_time
            ? Carbon::parse($date . ' ' . $requestData->end_time)
            : null;

        $attendance->update([
            'start_time' => $start,
            'end_time'   => $end,
        ]);

        // =====================
        // 休憩反映（datetime化）
        // =====================
        $breaks = $attendance->breakTimes;

        // 休憩1
        if (isset($breaks[0])) {
            $bStart1 = $requestData->break_start_1
                ? Carbon::parse($date . ' ' . $requestData->break_start_1)
                : null;

            $bEnd1 = $requestData->break_end_1
                ? Carbon::parse($date . ' ' . $requestData->break_end_1)
                : null;

            $breaks[0]->update([
                'start_time' => $bStart1,
                'end_time'   => $bEnd1,
            ]);
        }

        // 休憩2
        if (isset($breaks[1])) {
            $bStart2 = $requestData->break_start_2
                ? Carbon::parse($date . ' ' . $requestData->break_start_2)
                : null;

            $bEnd2 = $requestData->break_end_2
                ? Carbon::parse($date . ' ' . $requestData->break_end_2)
                : null;

            $breaks[1]->update([
                'start_time' => $bStart2,
                'end_time'   => $bEnd2,
            ]);
        }

        // =====================
        // 承認済み
        // =====================
        $requestData->update([
            'status' => 1
        ]);

        return redirect()->route('admin.request.index')
            ->with('message', '承認しました');
    }
        
    // 詳細表示（承認画面）
    public function show($id)
    {
        // 申請データ取得
        $requestData = StampCorrectionRequest::with('attendance.user')
            ->findOrFail($id);

        // 紐づく勤怠データ
        $attendance = $requestData->attendance;

        // 承認モード判定（申請がある場合）
        $isApproveMode = true;

        return view('shared.attendance_detail', compact(
            'attendance',
            'requestData',
            'isApproveMode'
        ));
    }


    //
}
