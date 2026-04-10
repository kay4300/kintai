@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/attendance.css') }}">
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

    <div class="attendance-table">
        <table>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>

            @foreach ($period as $day)
            @php
            $date = $day->format('Y-m-d');
            $attendance = $attendances[$date] ?? null;

            $breakTotal = 0;
            $workMinutes = 0;

            if ($attendance) {
            $breakTotal = $attendance->breaks->sum(function ($break) {
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
                <td>{{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}</td>
                <td>{{ floor($breakTotal / 60) }}時間{{ $breakTotal % 60 }}分</td>
                <td>{{ floor($workMinutes / 60) }}時間{{ $workMinutes % 60 }}分</td>

                <td>
                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                        <button>詳細</button>
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
        </table>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <a href="{{ route('admin.staff.csv', ['id' => $staff->id, 'month' => $currentMonth->format('Y-m')]) }}">
            <button>CSV出力</button>
        </a>
    </div>
</div>

@endsection