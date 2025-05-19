@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance-list-container">
    <h2 class="attendance-title">勤怠一覧</h2>

    <div class="attendance-filter">
        @php
            $prev = $currentMonth->copy()->subMonth();
            $next = $currentMonth->copy()->addMonth();
        @endphp

        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="prev-month">← 前月</a>
        <span class="current-month">{{ $currentMonth->format('Y/m') }}</span>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="next-month">翌月 →</a>
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
            <tr>
                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d(D)') }}</td>
                <td>{{ $attendance->start_time ?? '' }}</td>
                <td>{{ $attendance->end_time ?? '' }}</td>
                <td>{{ $attendance->break_duration ?? '' }}</td>
                <td>{{ $attendance->total_work_time ?? '' }}</td>
                <td><a href="{{ route('attendance.detail', ['id' => $attendance->id]) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
