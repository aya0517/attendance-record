@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    <form method="POST" action="{{ route('stamp_correction.store', $attendance->id) }}">
        @csrf

        <table class="attendance-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                    <strong>{{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}</strong>
                </td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time" name="start_time" value="{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}" class="time-box" {{ $pendingRequest ? 'readonly' : '' }}>
                    <span class="time-separator">〜</span>
                    <input type="time" name="end_time" value="{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}" class="time-box" {{ $pendingRequest ? 'readonly' : '' }}>
                </td>
            </tr>

            <tr>
                <th>休憩</th>
                <td>
                    <input type="time" name="break_start" value="{{ optional($attendance->breaks->first())->started_at ? \Carbon\Carbon::parse($attendance->breaks->first()->started_at)->format('H:i') : '' }}" class="time-box" {{ $pendingRequest ? 'readonly' : '' }}>
                    <span class="time-separator">〜</span>
                    <input type="time" name="break_end" value="{{ optional($attendance->breaks->first())->ended_at ? \Carbon\Carbon::parse($attendance->breaks->first()->ended_at)->format('H:i') : '' }}" class="time-box" {{ $pendingRequest ? 'readonly' : '' }}>
                </td>
            </tr>

            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note" class="note-area" {{ $pendingRequest ? 'readonly' : '' }}>{{ $attendance->note ?? '' }}</textarea>
                </td>
            </tr>
        </table>

        @if (!$pendingRequest)
            <div class="button-area">
                <button type="submit" class="edit-button">修正</button>
            </div>
        @else
            <p style="color: red; text-align: center; margin-top: 20px;">
                ※承認待ちのため修正はできません。
            </p>
        @endif

    </form>
</div>
@endsection
