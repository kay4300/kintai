<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = StampCorrectionRequest::where('user_id', Auth::id())
            ->when($status === 'pending', function ($query) {
                $query->where('status', 0);
            })
            ->when($status === 'approved', function ($query) {
                $query->where('status', 1);
            })
            ->latest()
            ->get();

        return view('staff.application.index', compact('requests', 'status'));
    }

    public function store(Request $request)
    {
        // バリデーション
        // $request->validate([
        //     'target_date' => 'required|date',
        //     'reason' => 'required|string|max:255',
        // ]);

        // 保存
        $attendance = Attendance::findOrFail($request->attendance_id);

        StampCorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'target_date' => $attendance->date,
            'reason' => $request->reason,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_start_1' => $request->break_start_1,
            'break_end_1' => $request->break_end_1,
            'break_start_2' => $request->break_start_2,
            'break_end_2' => $request->break_end_2,
            'status' => 0, // 承認待ち
        ]);
    
        return redirect()->back()->with('message', '修正申請を送信しました');
    }
}