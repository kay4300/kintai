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
        // dd($requests);
        return view('admin.request.index', compact('requests', 'status'));
    }
    //
}
