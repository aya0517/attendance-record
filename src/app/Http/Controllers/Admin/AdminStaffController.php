<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;


class AdminStaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('is_admin', false)->orderBy('name')->get();
        return view('admin.attendance.staffs_index', compact('staffs'));
    }

    public function showDetail(Request $request, $id)
{
    $staff = User::findOrFail($id);

    $currentMonth = $request->query('month')
        ? \Carbon\Carbon::parse($request->query('month'))
        : \Carbon\Carbon::now();

    $attendances = Attendance::where('user_id', $id)
        ->whereMonth('date', $currentMonth->month)
        ->whereYear('date', $currentMonth->year)
        ->orderBy('date', 'asc')
        ->with('breaks')
        ->get();

    return view('admin.attendance.staffs_detail', compact('staff', 'attendances', 'currentMonth'));
}

public function export($id)
{
    $staff = User::findOrFail($id);

    $attendances = Attendance::where('user_id', $id)
        ->orderBy('date', 'asc')
        ->with('breaks')
        ->get();

    $csv = "日付,出勤,退勤,休憩,合計\n";

    foreach ($attendances as $a) {
        $break = $a->breaks->sum(function ($b) {
            return $b->started_at && $b->ended_at
                ? Carbon::parse($b->ended_at)->diffInSeconds(Carbon::parse($b->started_at))
                : 0;
        });

        $breakFormatted = gmdate('H:i', $break);

        $total = $a->start_time && $a->end_time
            ? gmdate('H:i', Carbon::parse($a->end_time)->diffInSeconds(Carbon::parse($a->start_time)) - $break)
            : '';

        $csv .= $a->date . "," . $a->start_time . "," . $a->end_time . "," . $breakFormatted . "," . $total . "\n";
    }

    return Response::make($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$staff->name}_勤怠一覧.csv",
    ]);
}

}
