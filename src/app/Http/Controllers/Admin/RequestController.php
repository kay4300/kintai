<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\User;

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
        $requestData = StampCorrectionRequest::with('attendance.breaks')->findOrFail($id);

        // すでに承認済みなら何もしない
        if ($requestData->status == 1) {
            return back();
        }

        $attendance = $requestData->attendance;

        // 勤怠に反映
        $attendance->update([
            'start_time' => $requestData->start_time,
            'end_time' => $requestData->end_time,
        ]);

        // 休憩反映
        $breaks = $attendance->breaks;

        if (isset($breaks[0])) {
            $breaks[0]->update([
                'start_time' => $requestData->break_start_1,
                'end_time' => $requestData->break_end_1,
            ]);
        }

        if (isset($breaks[1])) {
            $breaks[1]->update([
                'start_time' => $requestData->break_start_2,
                'end_time' => $requestData->break_end_2,
            ]);
        }

        // 承認済みに変更
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
