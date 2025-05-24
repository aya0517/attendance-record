<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $attendance = Attendance::with(['user', 'breaks'])->findOrFail($id);
        return view('admin.attendance.detail', compact('attendance'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'start_time'   => 'nullable|date_format:H:i',
            'end_time'     => 'nullable|date_format:H:i|after_or_equal:start_time',
            'break_start'  => 'nullable|date_format:H:i',
            'break_end'    => 'nullable|date_format:H:i|after_or_equal:break_start',
            'note'         => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $id) {
            $attendance = Attendance::with('breaks')->findOrFail($id);

            $attendance->start_time = $request->input('start_time');
            $attendance->end_time = $request->input('end_time');
            $attendance->note = $request->input('note');
            $attendance->save();

            $break = $attendance->breaks->first();
            if ($break) {
                $break->started_at = $request->input('break_start');
                $break->ended_at = $request->input('break_end');
                $break->save();
            } else {
                $attendance->breaks()->create([
                    'started_at' => $request->input('break_start'),
                    'ended_at' => $request->input('break_end'),
                ]);
            }
        });

        return redirect()->route('admin.attendance.detail', $id);
}

}
