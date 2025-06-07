<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;
use App\Models\Attendance;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');
        $requests = StampCorrectionRequest::with('user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.attendance.requests_index', compact('requests', 'status'));
    }

    public function showApprove($requestId)
    {
        $request = \App\Models\StampCorrectionRequest::with(['user', 'attendance.breaks'])->findOrFail($requestId);
        $attendance = $request->attendance;

        return view('admin.attendance.requests_approve', [
            'attendance' => $attendance,
            'pendingRequest' => $request,
        ]);
    }


    public function approve($id)
    {
        $request = StampCorrectionRequest::findOrFail($id);

        $attendance = $request->attendance;

        $attendance->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,                'break_started_at' => $request->break_start,
            'break_ended_at' => $request->break_end,
            'note' => $request->note,
        ]);

        $request->status = 'approved';
        $request->save();

        return redirect()->route('admin.requests_approve', $request->id);
    }
}
