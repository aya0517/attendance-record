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

    /**
     * 勤怠の打刻処理（出勤・退勤・休憩入・休憩戻）
     */
    public function handleList(Request $request)
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
                $attendance->start_time = now()->format('H:i:s');
                $attendance->status = 'working';
                $attendance->on_break = false;
                break;

            case 'end':
                $attendance->end_time = now()->format('H:i:s');
                $attendance->status = 'ended';
                $attendance->on_break = false;
                break;

            case 'break_start':
                $attendance->on_break = true;
                $attendance->break_started_at = now()->format('H:i:s');
                $attendance->status = 'on_break';
                break;

            case 'break_end':
                $attendance->on_break = false;
                $attendance->break_ended_at = now()->format('H:i:s');
                $attendance->status = 'working';
                break;
        }

        $attendance->save();

        return redirect()->back();
    }
}
