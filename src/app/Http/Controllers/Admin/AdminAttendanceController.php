<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AttendanceRequest;

class AdminAttendanceController extends Controller
{
    public function index()
    {
        $currentDate = now();
        $prevDate = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDate = $currentDate->copy()->addDay()->format('Y-m-d');

        $attendances = Attendance::with('user')->orderBy('date', 'desc')->get();

        return view('admin.attendance.list', compact('attendances', 'currentDate', 'prevDate', 'nextDate'));
}


    public function showDaily(Request $request)
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.list', [
            'attendances' => $attendances,
            'currentDate' => $date,
            'prevDate' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $date->copy()->addDay()->format('Y-m-d'),
        ]);
    }

    public function showDetail($id)
    {
        $attendance = Attendance::with(['user', 'breaks', 'stamp_correction_requests'])->findOrFail($id);

        $pendingRequest = $attendance->stamp_correction_requests
            ->where('status', 'pending')
            ->isNotEmpty();

        return view('admin.attendance.detail', compact('attendance', 'pendingRequest'));
    }

    public function update(AttendanceRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $attendance = Attendance::with('breaks')->findOrFail($id);

            $attendance->start_time = $request->start_time;
            $attendance->end_time = $request->end_time;
            $attendance->note = $request->note;
            $attendance->save();

            $attendance->breaks()->delete();

            foreach ($request->input('breaks', []) as $break) {
                if (!empty($break['started_at']) && !empty($break['ended_at'])) {
                    $attendance->breaks()->create([
                        'started_at' => $attendance->date . ' ' . $break['started_at'],
                        'ended_at' => $attendance->date . ' ' . $break['ended_at'],
                    ]);
                }
            }
        });
        return redirect()->route('admin.attendance.detail', $id);
    }

}
