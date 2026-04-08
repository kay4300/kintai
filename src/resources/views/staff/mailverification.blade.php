@extends('layouts.app')

@section('title', 'メール認証 | kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mailverification.css') }}">
@endsection

@section('content')

<div class="container">
    <h2>メール認証完了</h2>

    <a href="{{ route('attendance') }}" class="btn-primary">
        勤怠登録へ
    </a>
</div>

@endsection