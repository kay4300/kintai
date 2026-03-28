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

            {{-- 管理者 --}}
            @php
            $route = Route::currentRouteName();
            @endphp

            @if(auth('admin')->check())
            <div class="header__nav">

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>

                {{-- 勤怠一覧 --}}
                @if($route === 'admin.dashboard')
                <span class="header__link active">勤怠一覧</span>
                @else
                <a href="{{ route('admin.dashboard') }}" class="header__link">勤怠一覧</a>
                @endif

                {{-- スタッフ一覧 --}}
                @if($route === 'admin.staff.index')
                <span class="header__link active">スタッフ一覧</span>
                @else
                <a href="{{ route('admin.staff.index') }}" class="header__link">スタッフ一覧</a>
                @endif

                {{-- 申請一覧 --}}
                @if($route === 'admin.request.index')
                <span class="header__link active">申請一覧</span>
                @else
                <a href="{{ route('admin.request.index') }}" class="header__link">申請一覧</a>
                @endif

            </div>
            @endif

            {{-- スタッフ --}}
            @php
            $route = Route::currentRouteName();
            @endphp
            
            @if(auth('web')->check())
            <div class="header__nav">

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>

                {{-- 勤怠 --}}
                @if($route === 'staff.attendance.create')
                <span class="header__link active">勤怠</span>
                @else
                <a href="{{ route('staff.attendance.create') }}" class="header__link">勤怠</a>
                @endif

                {{-- 勤怠一覧 --}}
                @if($route === 'staff.attendance.index')
                <span class="header__link active">勤怠一覧</span>
                @else
                <a href="{{ route('staff.attendance.index') }}" class="header__link">勤怠一覧</a>
                @endif

                {{-- 申請 --}}
                @if($route === 'staff.request.index')
                <span class="header__link active">申請</span>
                @else
                <a href="{{ route('staff.request.index') }}" class="header__link">申請</a>
                @endif
            </div>
            @endif
            @endif
        </div>
    </header>

    <main>
        @yield('content')
    </main>

</body>

</html>