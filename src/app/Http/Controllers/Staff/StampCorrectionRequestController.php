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
}