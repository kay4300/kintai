<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
{
    return view('admin.staff.index');
}

    public function attendance($id)
    {
        $staff = User::findOrFail($id);

        $attendances = Attendance::where('user_id', $id)->get();

        return view('admin.staff.attendance', compact('staff', 'attendances'));
    }
    //
}
