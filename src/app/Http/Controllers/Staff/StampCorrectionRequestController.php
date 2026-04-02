<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StampCorrectionRequest;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = StampCorrectionRequest::where('user_id', Auth::id())
            ->when($status === 'pending', function ($query) {
                $query->where('status', 1);
            })
            ->when($status === 'approved', function ($query) {
                $query->where('status', 2);
            })
            ->latest()
            ->get();

        return view('staff.application.index', compact('requests', 'status'));
    }

    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'target_date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        // 保存
        StampCorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $request->attendance_id,
            'target_date' => $request->target_date,
            'reason' => $request->reason,
            'status' => 1, // 承認待ち
        ]);

        return redirect()->back()->with('message', '修正申請を送信しました');
    }
}