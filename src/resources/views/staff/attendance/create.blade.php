@extends('layouts.app')

@section('content')
<div class="text-center">

    {{-- ステータス表示 --}}
    <p>
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
    <p>{{ now()->format('Y年n月j日（D）') }}</p>

    {{-- 時刻 --}}
    <h1>{{ now()->format('H:i') }}</h1>

    {{-- ボタン表示 --}}
    <div>

        {{-- 勤務外 --}}
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