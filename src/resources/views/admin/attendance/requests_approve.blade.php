@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/requests_approve.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

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
                <span class="time-box">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</span>
                <span class="time-separator">〜</span>
                <span class="time-box">{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}</span>
            </td>
        </tr>

        <tr>
            <th>休憩</th>
            <td>
                <span class="time-box">
                    {{ optional($attendance->breaks->first())->started_at ? \Carbon\Carbon::parse($attendance->breaks->first()->started_at)->format('H:i') : '--:--' }}
                </span>
                <span class="time-separator">〜</span>
                <span class="time-box">
                    {{ optional($attendance->breaks->first())->ended_at ? \Carbon\Carbon::parse($attendance->breaks->first()->ended_at)->format('H:i') : '--:--' }}
                </span>
            </td>
        </tr>

        <tr>
            <th>備考</th>
            <td>
                <div class="note-area" style="background-color: #f5f5f5; border: 1px solid #ddd;">
                    {{ $attendance->note ?? '（なし）' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="button-area" style="margin-top: 20px;">
        @if ($pendingRequest && $pendingRequest->status === 'pending')
            <form method="POST" action="{{ route('admin.attendance.approve', $pendingRequest->id) }}">
                @csrf
                <button type="submit" class="approved-button">承認</button>
            </form>
        @elseif ($pendingRequest && $pendingRequest->status === 'approved')
            <button class="approved-button approved-button--done" disabled>承認済み</button>
        @endif
    </div>
</div>
@endsection
