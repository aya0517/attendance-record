@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance-list-container">
    <h2 class="attendance-title">勤怠一覧</h2>

    <div class="attendance-filter">
        <a href="{{ url('/attendance/list') . '?month=' . $prevMonth }}" class="prev-month">← 前月</a>
        <span class="current-month">{{ $currentMonth->format('Y/m') }}</span>
        <a href="{{ url('/attendance/list') . '?month=' . $nextMonth }}" class="next-month">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            @php
                $breakSeconds = $attendance->breaks->sum(function ($break) {
                    return $break->started_at && $break->ended_at
                        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $break->ended_at)
                            ->diffInSeconds(\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $break->started_at))
                        : 0;
                });

                $totalWorkSeconds = ($attendance->start_time && $attendance->end_time)
                    ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->end_time)
                        ->diffInSeconds(\Carbon\Carbon::createFromFormat('H:i:s', $attendance->start_time)) - $breakSeconds
                    : null;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d(D)') }}</td>
                <td>{{ $attendance->start_time ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->start_time)->format('H:i') : '' }}</td>
                <td>{{ $attendance->end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->end_time)->format('H:i') : '' }}</td>
                <td>{{ gmdate('H:i', $breakSeconds) }}</td>
                <td>{{ $totalWorkSeconds !== null ? gmdate('H:i', $totalWorkSeconds) : '' }}</td>
                <td><a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection