@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="status-badge">
        @switch($status)
            @case('working') 勤務中 @break
            @case('on_break') 休憩中 @break
            @case('ended') 退勤済 @break
            @default 勤務外
        @endswitch
    </div>

    <div class="date-display">
        {{ \Carbon\Carbon::now()->format('Y年n月j日(D)') }}
    </div>

    <div class="time-display">
        {{ \Carbon\Carbon::now()->format('H:i') }}
    </div>

    <form method="POST" action="{{ route('attendance.list') }}">
        @csrf

        @if ($status === 'off')
            <button type="submit" name="action" value="start" class="main-button">出勤</button>

        @elseif ($status === 'working')
            <div class="button-group">
                <button type="submit" name="action" value="end" class="main-button">退勤</button>
                <button type="submit" name="action" value="break_start" class="main-button">休憩入</button>
            </div>

        @elseif ($status === 'on_break')
            <button type="submit" name="action" value="break_end" class="main-button">休憩戻</button>

        @elseif ($status === 'ended')
            <p class="thank-message">お疲れ様でした。</p>
        @endif
    </form>
</div>
@endsection
