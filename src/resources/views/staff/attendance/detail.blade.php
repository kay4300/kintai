@extends('layouts.app')

@section('content')
<div>

    <h2>勤怠詳細</h2>

    <form method="POST" action="{{ route('staff.attendance.update', $attendance->id) }}">
        @csrf
        @method('PUT')

        {{-- 名前 --}}
        <div>
            <div>名前</div>
            <div>{{ $attendance->user->name }}</div>
        </div>

        {{-- 日付 --}}
        <div>
            <div>日付</div>
            <div>
                {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
            </div>
        </div>

        {{-- 出勤・退勤 --}}
        <div>
            <div>出勤・退勤</div>
            <div>
                <input type="time" name="start_time"
                    value="{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}">

                〜

                <input type="time" name="end_time"
                    value="{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}">
            </div>
        </div>

        @php
            $breaks = $attendance->breaks;
        @endphp

        {{-- 休憩 --}}
        <div>
            <div>休憩</div>
            <div>
                <input type="time" name="break_start_1"
                    value="{{ isset($breaks[0]) && $breaks[0]->start_time ? \Carbon\Carbon::parse($breaks[0]->start_time)->format('H:i') : '' }}">

                〜

                <input type="time" name="break_end_1"
                    value="{{ isset($breaks[0]) && $breaks[0]->end_time ? \Carbon\Carbon::parse($breaks[0]->end_time)->format('H:i') : '' }}">
            </div>
        </div>

        {{-- 休憩2 --}}
        <div>
            <div>休憩2</div>
            <div>
                <input type="time" name="break_start_2"
                    value="{{ isset($breaks[1]) && $breaks[1]->start_time ? \Carbon\Carbon::parse($breaks[1]->start_time)->format('H:i') : '' }}">

                〜

                <input type="time" name="break_end_2"
                    value="{{ isset($breaks[1]) && $breaks[1]->end_time ? \Carbon\Carbon::parse($breaks[1]->end_time)->format('H:i') : '' }}">
            </div>
        </div>

        {{-- 備考 --}}
        <div>
            <div>備考</div>
            <div>
                <textarea name="note" rows="3">{{ $attendance->note ?? '' }}</textarea>
            </div>
        </div>

        {{-- 修正ボタン --}}
        <div>
            <button type="submit">修正</button>
        </div>

    </form>

</div>
@endsection