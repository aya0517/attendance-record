<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->get();
        return view('admin.attendance.list', compact('attendances'));
    }
}
