@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@yield('css')
@endsection

@section('content')

<h1>{{ $currentDate }}の勤怠</h1>
<div class="container">

    <!-- 月ナビゲーション -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <a href="{{ route('admin.dashboard', ['date' => $prevDate]) }}">
            ← 前日
        </a>

        <div class="mx-3 d-flex align-items-center">
            <span class="me-2">📅</span>
            <strong>{{ $currentMonth }}</strong>
        </div>

        <a href="{{ route('admin.dashboard', ['date' => $nextDate]) }}" class="btn btn-light">
            翌日 →
        </a>
    </div>
</div>

<div class="container">

    <table>
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