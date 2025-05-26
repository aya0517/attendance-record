@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staffs_index.css') }}">
@endsection

@section('content')
<div class="staff-list-container">
    <h2 class="page-title">スタッフ一覧</h2>

    <table class="staff-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($staffs as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <a href="{{ route('admin.staffs.detail', $staff->id) }}">詳細</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">スタッフが見つかりません。</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
