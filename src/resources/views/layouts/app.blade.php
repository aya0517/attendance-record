<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>attendance-record</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
    <div class="header__inner">
        <a href="{{ url('/') }}" class="header__logo">
            <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo-image">
        </a>

        @if (Auth::guard('admin')->check())
            <nav class="header__nav">
                <ul class="header-nav">
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('admin.staffs.index') }}">スタッフ一覧</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('admin.attendance.requests') }}">申請一覧</a>
                    </li>
                    <li class="header-nav__item">
                        <form action="{{ route('admin.logout') }}" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        @elseif (Auth::guard('web')->check())
            <nav class="header__nav">
                <ul class="header-nav">
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('attendance') }}">勤怠</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('attendance.list') }}">勤怠一覧</a>
                    </li>
                    <li class="header-nav__item">
                        <a class="header-nav__link" href="{{ route('stamp_correction.list') }}">申請</a>
                    </li>
                    <li class="header-nav__item">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        @endif

    </div>
</header>


    <main>
        @yield('content')
    </main>
</body>

</html>

