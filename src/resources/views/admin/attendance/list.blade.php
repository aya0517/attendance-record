@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance-list-container">
    <h2 class="attendance-title">{{ $currentDate->format('Y年n月j日') }}の勤怠</h2>

    <div class="attendance-filter">
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}" class="prev"> 前日</a>
        <span class="current-month">{{ $currentDate->format('Y/m/d') }}</span>
        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="next">翌日 </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
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
                            ? \Carbon\Carbon::parse($break->ended_at)->diffInSeconds(\Carbon\Carbon::parse($break->started_at))
                            : 0;
                    });

                    $totalWorkSeconds = ($attendance->start_time && $attendance->end_time)
                        ? \Carbon\Carbon::parse($attendance->end_time)->diffInSeconds(\Carbon\Carbon::parse($attendance->start_time)) - $breakSeconds
                        : null;
                @endphp
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}</td>
                    <td>{{ gmdate('H:i', $breakSeconds) }}</td>
                    <td>{{ $totalWorkSeconds !== null ? gmdate('H:i', $totalWorkSeconds) : '' }}</td>
                    <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
