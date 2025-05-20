@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="admin-attendance-list-container">
    <h1>勤怠一覧</h1>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>ユーザー名</th>
                <th>日付</th>
                <th>出勤時刻</th>
                <th>退勤時刻</th>
                <th>休憩時間</th>
                <th>合計勤務時間</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
                    <td>{{ $attendance->start_time ?? '--:--' }}</td>
                    <td>{{ $attendance->end_time ?? '--:--' }}</td>
                    <td>
                        @php
                            $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                                if ($break->started_at && $break->ended_at) {
                                    return \Carbon\Carbon::parse($break->ended_at)
                                        ->diffInSeconds(\Carbon\Carbon::parse($break->started_at));
                                }
                                return 0;
                            });
                        @endphp
                        {{ gmdate('H:i', $totalBreakSeconds) }}
                    </td>
                    <td>
                        @if ($attendance->start_time && $attendance->end_time)
                            @php
                                $workSeconds = \Carbon\Carbon::parse($attendance->end_time)
                                    ->diffInSeconds(\Carbon\Carbon::parse($attendance->start_time)) - $totalBreakSeconds;
                            @endphp
                            {{ gmdate('H:i', $workSeconds) }}
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('attendance.detail', $attendance->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
