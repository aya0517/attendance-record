@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/requests_index.css') }}">
@endsection

@section('content')
<div class="request-list-container">
    <h2 class="page-title">申請一覧</h2>

    <div class="tab-menu">
        <a href="?status=pending" class="{{ request('status', 'pending') === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="?status=approved" class="{{ request('status') === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->date)->format('Y/m/d') }}</td>
                    <td>{{ $request->note }}</td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('attendance.detail', $request->attendance_id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">申請はありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
