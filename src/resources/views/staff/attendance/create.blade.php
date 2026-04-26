@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/staff/create.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    @php
    $isFinished = $attendance && $attendance->status === 3;
    @endphp

    {{-- ステータス表示 --}}
    <p class="status-label">
        <span>
            @if(!$attendance)
            勤務外
            @elseif($attendance->status === 1)
            出勤中
            @elseif($attendance->status === 2)
            休憩中
            @elseif($attendance->status === 3)
            退勤済
            @endif
        </span>
    </p>

    {{-- 日付 --}}
    <p class="date-text">{{ now()->locale('ja')->isoFormat('YYYY年M月D日（ddd）') }}</p>

    {{-- 時刻 --}}
    
    <h1 id="clock" class="clock"></h1>

    <script>
        function updateClock() {
            const now = new Date();

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            document.getElementById('clock').textContent = hours + ':' + minutes;
        }

        // 初回実行
        updateClock();

        // 1秒ごと更新
        setInterval(updateClock, 1000);
    </script>

    {{-- ボタン表示 --}}
    <div class="button-area">
        @if(!$attendance)
        {{-- 出勤前（まだ打刻なし） --}}
        <form method="POST" action="{{ route('attendance.start') }}">
            @csrf
            <button type="submit" class="action-btn btn-black">出勤</button>
        </form>

        @elseif($attendance->status === 1)
        <div class="flex-buttons">
            {{-- 出勤中 --}}
            <form method="POST" action="{{ route('attendance.end') }}">
                @csrf
                <button type="submit" class="action-btn btn-end">退勤</button>
            </form>

            <form method="POST" action="{{ route('break.start') }}">
                @csrf
                <button type="submit" class="action-btn btn-break">休憩入</button>
            </form>
        </div>
        @elseif($attendance->status === 2)
        {{-- 休憩中 --}}
        <form method="POST" action="{{ route('break.end') }}">
            @csrf
            <button type="submit" class="action-btn btn-return">休憩戻</button>
        </form>

        @elseif($attendance->status === 3)
        {{-- 退勤済（＝今日の打刻は終了） --}}
        <p class="finish-text">お疲れさまでした。</p>
        @endif

    </div>
    @endsection