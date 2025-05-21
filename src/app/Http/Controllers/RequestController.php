<?php

namespace App\Http\Controllers;

use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Requests\AttendanceRequest;


class RequestController extends Controller
{
    public function store(AttendanceRequest $request, Attendance $attendance)
    {
        \Log::info('Request method', ['method' => request()->method()]);

        StampCorrectionRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'date' => $attendance->date,
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
            'break_start' => $request->input('break_start'),
            'break_end' => $request->input('break_end'),
            'note' => $request->input('note'),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('attendance.detail', $attendance->id);
    }


    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status', 'pending');

        $query = StampCorrectionRequest::with(['user', 'attendance'])
                    ->where('status', $status);

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return view('requests_index', compact('requests', 'status'));
    }

    public function adminRequests(Request $request)
    {
        $status = $request->query('status', 'pending');

        $requests = StampCorrectionRequest::with(['user', 'attendance'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.attendance.requests_index', compact('requests', 'status'));
    }

}
