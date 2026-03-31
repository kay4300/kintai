@extends('layouts.app')

@section('title', 'kintai')

@section('content')

<h1>勤怠一覧</h1>
<div class="container">

    <!-- 月ナビゲーション -->
    <div class="d-flex justify-content-center align-items-center mb-4">
        <a href="{{ route('staff.attendance.list', ['month' => $prevMonth]) }}" class="btn btn-light">
            ← 前月
        </a>

        <div class="mx-3 d-flex align-items-center">
            <span class="me-2">📅</span>
            <strong>{{ $currentMonth }}</strong>
        </div>

        <a href="{{ route('staff.attendance.list', ['month' => $nextMonth]) }}" class="btn btn-light">
            翌月 →
        </a>
    </div>

    <!-- 勤怠一覧 -->
    <table class="table table-bordered text-center">
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
                <td>{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}</td>

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