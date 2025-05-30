@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login__content">
    <div class="login-form__heading">
        <h2>管理者ログイン</h2>
    </div>
    <form class="form" action="{{ url('/admin/login') }}" method="post">
        @csrf
        <div class="form__group">
            <div class="form__group-title">メールアドレス</div>
            <div class="form__input--text">
                <input type="email" name="email" value="{{ old('email') }}" />
            </div>
            <div class="form__error">@error('email'){{ $message }}@enderror</div>
        </div>

        <div class="form__group">
            <div class="form__group-title">パスワード</div>
            <div class="form__input--text">
                <input type="password" name="password" />
            </div>
            <div class="form__error">@error('password'){{ $message }}@enderror</div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">管理者ログイン</button>
        </div>
    </form>
</div>
@endsection
