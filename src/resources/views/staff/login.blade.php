@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/staff/login.css') }}">
@endsection

@section('content')

<div class="register-form__content">
    <div class="register-form__heading">
        <h1>ログイン</h1>
    </div>

    <form class="form" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">メールアドレス</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="email" placeholder="例 test@example.com" value="{{ old('email') }}" />
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">パスワード</span>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" name="password" placeholder="xxxxxxxx" />
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>

        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
            <a href="{{ route('register') }}" class="form__button-submit">
                会員登録はこちら
            </a>
        </div>
    </form>
</div>

@endsection