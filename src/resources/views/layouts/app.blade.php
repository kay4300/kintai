<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'kintai')</title>

    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>

    <header class="header">
        <div class="header__inner">
            <div class="header__logo">COACHTECH</div>

            <!-- 右側ナビは特定ページのみ表示 -->
            @if(
            !in_array(Route::currentRouteName(), ['login', 'register', 'mailenable', 'mailverification'])
            && !(Route::currentRouteName() === 'top' && !auth()->check())
            )
            @auth
            <div class="header__nav">
                <form action="{{ route('logout') }}" method="POST" class="header__logout-form">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
                <!-- 管理者ナビ -->
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.attendance.index') }}" class="header__link">勤怠一覧</a>
                <a href="{{ route('admin.staff.index') }}" class="header__link">スタッフ一覧</a>
                <a href="{{ route('admin.request.index') }}" class="header__link">申請一覧</a>
                @endif

                <!-- スタッフナビ -->
                @if(auth()->user()->role === 'staff')
                <a href="{{ route('attendance') }}" class="header__link">勤怠</a>
                
                @endif
            </div>
            @endauth
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>