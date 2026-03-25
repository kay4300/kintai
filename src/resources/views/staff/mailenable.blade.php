@extends('layouts.app')

@section('title', 'メール認証 | kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mailenable.css') }}">
@endsection

@section('content')

<div class="verify">

    <p>
        登録したメールアドレスに認証メールを送信しました。<br>
        メール内のリンクをクリックしてください。
    </p>

    <div class="verify__button-wrapper">
        <!-- 認証済みフォーム送信 -->
        <a href="http://localhost:8025" target="_blank">
            認証はこちらから
        </a>
    </div>

    <div class="verify__resend">
        <form method="post" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="verify__resend-link">
                認証メールを再送する
            </button>
        </form>
    </div>

</div>

@endsection