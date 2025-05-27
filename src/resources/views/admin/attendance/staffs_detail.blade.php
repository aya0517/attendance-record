@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staffs_detail.css') }}">
@endsection

@section('content')
<div class="staff-detail-container">
    <h2 class="page-title">{{ $staff->name }}さんの勤怠</h2>

    <div class="date-navigation-container">
    <div class="month-navigation">
        <a href="{{ route('admin.staffs.detail', [$staff->id, 'month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}">&larr; 前月</a>
        <span class="current-month">{{ $currentMonth->format('Y/m') }}</span>
        <a href="{{ route('admin.staffs.detail', [$staff->id, 'month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}">翌月 &rarr;</a>
    </div>
    </div>

    <div class="attendance-table-container">
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
                        return ($break->started_at && $break->ended_at)
                            ? \Carbon\Carbon::parse($break->ended_at)->diffInSeconds($break->started_at)
                            : 0;
                    });
                    $totalWorkSeconds = ($attendance->start_time && $attendance->end_time)
                        ? \Carbon\Carbon::parse($attendance->end_time)->diffInSeconds($attendance->start_time) - $breakSeconds
                        : 0;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('m/d(D)') }}</td>
                    <td>{{ $attendance->start_time ?? '' }}</td>
                    <td>{{ $attendance->end_time ?? '' }}</td>
                    <td>{{ gmdate('H:i', $breakSeconds) }}</td>
                    <td>{{ gmdate('H:i', $totalWorkSeconds) }}</td>
                    <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="csv-button-area">
        <form method="GET" action="{{ route('admin.staffs.export', $staff->id) }}">
            <button type="submit" class="csv-button">CSV出力</button>
        </form>
    </div>
</div>
@endsection