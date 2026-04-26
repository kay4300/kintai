@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/staff/index.css') }}">
@endsection

@section('content')

<div class="attendance-container">
    <h1 class="page-title">勤怠一覧</h1>
    <!-- 月ナビゲーション -->
    <div class="month-nav">
        <a href="{{ route('staff.attendance.list', ['month' => $prevMonth]) }}" class="btn btn-light">
            ← 前月
        </a>

        <div class="month-current">
            <span class="me-2">📅</span>
            <strong>{{ $currentMonth }}</strong>
        </div>

        <a href="{{ route('staff.attendance.list', ['month' => $nextMonth]) }}" class="btn btn-light">
            翌月 →
        </a>
    </div>

    <!-- 勤怠一覧 -->
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
            @foreach ($calendar as $day)
            @php
            $attendance = $day['attendance'];
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($day['date'])->isoFormat('MM/DD (dd)') }}</td>

                @if ($attendance)
                <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                
                <td>
                    {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}
                </td>

                <!-- 休憩合計 -->
                <td>
                    {{ intdiv($day['total_break'], 60) }}時間{{ $day['total_break'] % 60 }}分
                </td>

                <!-- 実働時間 -->
                <td>
                    {{ intdiv($day['work_minutes'], 60) }}時間{{ $day['work_minutes'] % 60 }}分
                </td>

                <td>
                    <a href="{{ route('staff.attendance.detail', $attendance->id) }}">詳細</a>
                </td>
                @else
                <td colspan="5">-</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection