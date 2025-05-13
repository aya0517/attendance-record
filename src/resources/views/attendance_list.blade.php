@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance-list-container">
    <h2 class="attendance-title">勤怠一覧</h2>

    <div class="attendance-filter">
        <button class="prev-month">← 前月</button>
        <span class="current-month">{{ \Carbon\Carbon::now()->format('Y/m') }}</span>
        <button class="next-month">翌月 →</button>
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
                <td>{{ $attendance->start_time ?? '-' }}</td>
                <td>{{ $attendance->end_time ?? '-' }}</td>
                <td>{{ $attendance->break_duration ?? '-' }}</td>
                <td>{{ $attendance->total_work_time ?? '-' }}</td>
                <td><a href="#">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
