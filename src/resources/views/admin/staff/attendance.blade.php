@extends('layouts.app')

@section('title', 'admin.kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <h1 class="page-title">{{ $staff->name }} さんの勤怠一覧</h1>


    <div class="month-nav">
        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $prevMonth]) }}">
            ← 前月
        </a>
        <div class="month-current">
            <span>📅</span>
            <strong>{{ $currentMonth->format('Y年n月') }}</strong>
        </div>

        <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $nextMonth]) }}">
            翌月 →
        </a>
    </div>


    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($period as $day)
            @php
            $date = $day->format('Y-m-d');
            $attendance = $attendances[$date] ?? null;

            $breakTotal = 0;
            $workMinutes = 0;

            if ($attendance) {
            $breakTotal = optional($attendance->breakTimes)->sum(function ($break) {
            return \Carbon\Carbon::parse($break->end_time)
            ->diffInMinutes(\Carbon\Carbon::parse($break->start_time));
            });

            if ($attendance->start_time && $attendance->end_time) {
            $workMinutes = \Carbon\Carbon::parse($attendance->end_time)
            ->diffInMinutes(\Carbon\Carbon::parse($attendance->start_time)) - $breakTotal;
            }
            }
            @endphp

            <tr>
                <td>{{ $date }}</td>

                @php
                $isFuture = \Carbon\Carbon::parse($date)->isFuture();
                @endphp

                @if ($attendance)
                <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>

                <td>
                    {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
                </td>
                <td>{{ floor($breakTotal / 60) }}時間{{ $breakTotal % 60 }}分</td>
                <td>{{ floor($workMinutes / 60) }}時間{{ $workMinutes % 60 }}分</td>

                <td>
                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                        詳細
                    </a>
                </td>

                <!-- 通常表示 -->
                @elseif ($isFuture)
                <td colspan="4"></td>
                <td></td>
                @else
                <td colspan="4">休み</td>
                <td>-</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>


    <div class="csv-button">
        <a href="{{ route('admin.staff.csv', ['id' => $staff->id, 'month' => $currentMonth->format('Y-m')]) }}" class="form__button-submit">
            CSV出力
        </a>
    </div>
</div>

@endsection