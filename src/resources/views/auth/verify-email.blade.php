@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="container" style="max-width: 600px; margin: 50px auto; text-align: center;">
    <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
    <p>メール認証を完了してください。</p>

    <form action="https://mailtrap.io/inboxes" method="get" target="_blank">
    <button type="submit" class="verify-button">
        認証はこちらから
    </button>
    </form>

    <a href="{{ route('verification.send') }}"
        onclick="event.preventDefault(); document.getElementById('resend-form').submit();"
        class="resend-link">
    認証メールを再送する
    </a>

    <form id="resend-form" action="{{ route('verification.send') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
@endsection
