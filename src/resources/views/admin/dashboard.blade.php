@extends('layouts.app')

@section('title', 'admin.kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">

@endsection

@section('content')

<div class="attendance-container">

    <h1 class="page-title">{{ $currentDate }}の勤怠</h1>

    <!-- 月ナビゲーション -->
    <div class="month-nav">
        <a href="{{ route('admin.dashboard', ['date' => $prevDate]) }}" class="nav-btn">
            ← 前日
        </a>

        <div class="month-current">
            <span>📅</span>
            <strong>{{ $currentDate }}</strong>
        </div>

        <a href="{{ route('admin.dashboard', ['date' => $nextDate]) }}" class="nav-btn">
            翌日 →
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            @php
            // 休憩合計（分）
            $breakTotal = $attendance->breakTimes->sum(function ($break) {
            return \Carbon\Carbon::parse($break->start_time)
            ->diffInMinutes(\Carbon\Carbon::parse($break->end_time));
            });

            // 勤務時間（分）
            $workMinutes = 0;
            if ($attendance->start_time && $attendance->end_time) {
            $workMinutes =
            \Carbon\Carbon::parse($attendance->start_time)
            ->diffInMinutes(\Carbon\Carbon::parse($attendance->end_time))
            - $breakTotal;
            }
            @endphp

            <tr>
                <td>{{ $attendance->user->name }}</td>

                <td>{{ $attendance->start_time_formatted }}</td>
                <td>{{ $attendance->end_time_formatted }}</td>

                <td>
                    {{ floor($breakTotal / 60) }}:
                    {{ str_pad($breakTotal % 60, 2, '0', STR_PAD_LEFT) }}
                </td>

                <td>
                    {{ floor($workMinutes / 60) }}:
                    {{ str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT) }}
                </td>

                <td>
                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection