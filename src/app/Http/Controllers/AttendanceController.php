<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * 勤怠画面の表示（ステータスに応じたボタン切り替え）
     */
    public function index()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->latest()
            ->first();

        \Log::info('取得した勤怠データ', [
        'id' => optional($attendance)->id,
        'date' => optional($attendance)->date,
        'status' => optional($attendance)->status,
        ]);

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

        \Log::info('判定されたステータス', ['status' => $status]);

        return view('attendance', compact('status'));
    }

    /**
     * 勤怠の打刻処理（出勤・退勤・休憩入・休憩戻）
     */
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


    public function showList()
    {
        $user = Auth::user();

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
                $attendance->total_work_time = '-';
            }
        }

        return view('attendance_list', compact('attendances'));
    }

}
