<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequest;
use App\Models\StampCorrectionRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->latest()
            ->first();

        if (!$attendance) {
            $status = 'off';
        } elseif ($attendance->status === 'ended') {
            $status = 'ended';
        } elseif ($attendance->on_break) {
            $status = 'on_break';
        } elseif ($attendance->status === 'working') {
            $status = 'working';
        } else {
            $status = 'off';
        }

        return view('attendance', compact('status'));
    }

    public function handlePunch(Request $request)
    {
        $action = $request->input('action');
        $user = Auth::user();

        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);

        $attendance->date = now()->toDateString();

        switch ($action) {
            case 'start':
                if (!$attendance->start_time) {
                    $attendance->start_time = now()->format('H:i:s');
                    $attendance->status = 'working';
                    $attendance->on_break = false;
                    $attendance->save();
                }
                break;

            case 'end':
                if (!$attendance->end_time) {
                    $attendance->end_time = now()->format('H:i:s');
                    $attendance->status = 'ended';
                    $attendance->on_break = false;
                    $attendance->save();
                }
                break;

            case 'break_start':
                $attendance->on_break = true;
                $attendance->break_started_at = now()->format('H:i:s');
                $attendance->status = 'on_break';
                $attendance->save();

                $attendance->breaks()->create([
                    'started_at' => now()->format('H:i:s'),
                ]);
                return redirect()->back();

            case 'break_end':
                $attendance->on_break = false;
                $attendance->break_ended_at = now()->format('H:i:s');
                $attendance->status = 'working';
                $attendance->save();

                $latestBreak = $attendance->breaks()->whereNull('ended_at')->latest()->first();
                if ($latestBreak) {
                    $latestBreak->update([
                        'ended_at' => now()->format('H:i:s'),
                    ]);
                }
                return redirect()->back();
        }

        $attendance->save();
        return redirect()->back();
    }

    public function showList(Request $request)
    {
        $user = Auth::user();

        $monthParam = $request->input('month');

        $targetDate = $monthParam
        ? \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
        : now()->startOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->orderBy('date')
            ->get();

        foreach ($attendances as $attendance) {
            $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                if ($break->started_at && $break->ended_at) {
                    return \Carbon\Carbon::parse($break->ended_at)->diffInSeconds(\Carbon\Carbon::parse($break->started_at));
                }
                return 0;
            });

            $attendance->break_duration = gmdate('H:i', $totalBreakSeconds);

            if ($attendance->start_time && $attendance->end_time) {
                $totalWorkSeconds = \Carbon\Carbon::parse($attendance->end_time)->diffInSeconds(\Carbon\Carbon::parse($attendance->start_time)) - $totalBreakSeconds;
                $attendance->total_work_time = gmdate('H:i', $totalWorkSeconds);
            } else {
                $attendance->total_work_time = '';
            }
        }

        return view('attendance_list', [
        'attendances' => $attendances,
        'currentMonth' => $targetDate,
        'prevMonth' => $targetDate->copy()->subMonth()->format('Y-m'),
        'nextMonth' => $targetDate->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function showDetail($id)
    {
        $attendance = Attendance::with('breaks')->findOrFail($id);

        $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
            if ($break->started_at && $break->ended_at) {
                return \Carbon\Carbon::parse($break->ended_at)->diffInSeconds(\Carbon\Carbon::parse($break->started_at));
            }
            return 0;
        });

        $breakDuration = gmdate('H:i', $totalBreakSeconds);

        if ($attendance->start_time && $attendance->end_time) {
            $totalWorkSeconds = \Carbon\Carbon::parse($attendance->end_time)->diffInSeconds(\Carbon\Carbon::parse($attendance->start_time)) - $totalBreakSeconds;
            $totalWorkTime = gmdate('H:i', $totalWorkSeconds);
        } else {
            $totalWorkTime = '';
        }

        $pendingRequest = StampCorrectionRequest::where('attendance_id', $attendance->id)
            ->where('status', 'pending')
            ->exists();

        return view('attendance_detail', compact('attendance', 'breakDuration', 'totalWorkTime', 'pendingRequest'));
    }

}
