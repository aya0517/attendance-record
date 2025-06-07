@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>


    <form method="POST" action="{{ url('/admin/attendance/' . $attendance->id) }}">
        @csrf
        @method('PATCH')

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
                    <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}" class="time-box">
                    <span class="time-separator">〜</span>
                    <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($attendance->end_time)->format('H:i')) }}" class="time-box">
                    @if ($errors->has('start_time'))
                        <p class="error-text">{{ $errors->first('start_time') }}</p>
                    @endif
                    @if ($errors->has('end_time'))
                        <p class="error-text">{{ $errors->first('end_time') }}</p>
                    @endif
                </td>
            </tr>

            @for ($i = 0; $i < 2; $i++)
            <tr>
                <th>休憩{{ $i + 1 }}</th>
                <td>
                    <input type="time" name="breaks[{{ $i }}][started_at]"
                        value="{{ old("breaks.$i.started_at", isset($attendance->breaks[$i]) && $attendance->breaks[$i]->started_at ? \Carbon\Carbon::parse($attendance->breaks[$i]->started_at)->format('H:i') : '') }}"
                        class="time-box">

                    <span class="time-separator">〜</span>

                    <input type="time" name="breaks[{{ $i }}][ended_at]"
                        value="{{ old("breaks.$i.ended_at", isset($attendance->breaks[$i]) && $attendance->breaks[$i]->ended_at ? \Carbon\Carbon::parse($attendance->breaks[$i]->ended_at)->format('H:i') : '') }}"
                        class="time-box">

                    @if ($errors->has("breaks.$i.started_at"))
                        <p class="error-text">{{ $errors->first("breaks.$i.started_at") }}</p>
                    @endif
                    @if ($errors->has("breaks.$i.ended_at"))
                        <p class="error-text">{{ $errors->first("breaks.$i.ended_at") }}</p>
                    @endif
                </td>
            </tr>
            @endfor

            <tr>
                <th>備考</th>
                <td>
                    <textarea name="note" class="note-area">{{ old('note', $attendance->note ?? '') }}</textarea>
                    @if ($errors->has('note'))
                        <p class="error-text">{{ $errors->first('note') }}</p>
                    @endif
                </td>
            </tr>
        </table>

        @if (Auth::guard('admin')->check() || !$pendingRequest)
            <div class="button-area">
                <button type="submit" class="edit-button">修正</button>
            </div>
        @endif
    </form>
</div>
@endsection
