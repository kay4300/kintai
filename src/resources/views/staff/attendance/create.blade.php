@extends('layouts.app')

@section('title', 'kintai')

@section('content')
<div class="text-center">

    @php
    $isFinished = $attendance && $attendance->status === 3;
    @endphp

    {{-- ステータス表示 --}}
    <p>
        <!-- @if(!$attendance || $isFinished)
        勤務外
        @elseif($attendance->status === 1)
        出勤中
        @elseif($attendance->status === 2)
        休憩中
        @endif -->
        @if(!$attendance)
        勤務外
        @elseif($attendance->status === 1)
        出勤中
        @elseif($attendance->status === 2)
        休憩中
        @elseif($attendance->status === 3)
        退勤済
        @endif
    </p>

    {{-- 日付 --}}
    <p>{{ now()->locale('ja')->isoFormat('YYYY年M月D日（ddd）') }}</p>

    {{-- 時刻 --}}
    <!-- <h1>{{ now()->format('H:i') }}</h1> -->
    <h1 id="clock"></h1>

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
    <div>

        {{-- 出勤前 --}}
        @if(!$attendance)
        <form method="POST" action="{{ route('attendance.start') }}">
            @csrf
            <button type="submit">出勤</button>
        </form>
        @endif

        {{-- 出勤中 --}}
        @if($attendance && $attendance->status === 1)
        <form method="POST" action="{{ route('attendance.end') }}">
            @csrf
            <button type="submit">退勤</button>
        </form>

        <form method="POST" action="{{ route('break.start') }}">
            @csrf
            <button type="submit">休憩入</button>
        </form>
        @endif

        {{-- 休憩中 --}}
        @if($attendance && $attendance->status === 2)
        <form method="POST" action="{{ route('break.end') }}">
            @csrf
            <button type="submit">休憩戻</button>
        </form>
        @endif

        {{-- 退勤済 --}}
        @if($attendance && $attendance->status === 3)
        <p>お疲れ様でした。</p>
        @endif

    </div>

</div>
@endsection