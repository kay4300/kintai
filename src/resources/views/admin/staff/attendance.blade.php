@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staffindex.css') }}">
@endsection

@section('content')

<h2>{{ $staff->name }} さんの勤怠一覧</h2>

@php
$current = \Carbon\Carbon::parse($month);
@endphp

<div>
    <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $current->copy()->subMonth()->format('Y-m')]) }}">
        ←前月
    </a>

    <span>
        📅 {{ $current->format('Y年n月') }}
    </span>

    <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $current->copy()->addMonth()->format('Y-m')]) }}">
        翌月→
    </a>

    <table>
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>

        @foreach ($attendances as $attendance)
        @php
        $breakTotal = $attendance->breaks->sum(function ($break) {
        return \Carbon\Carbon::parse($break->end_time)
        ->diffInMinutes(\Carbon\Carbon::parse($break->start_time));
        });

        $workMinutes = 0;
        if ($attendance->start_time && $attendance->end_time) {
        $workMinutes = \Carbon\Carbon::parse($attendance->end_time)
        ->diffInMinutes(\Carbon\Carbon::parse($attendance->start_time)) - $breakTotal;
        }
        @endphp

        <tr>
            <td>{{ $attendance->date }}</td>
            <td>{{ $attendance->start_time }}</td>
            <td>{{ $attendance->end_time }}</td>

            <td>{{ floor($breakTotal / 60) }}時間{{ $breakTotal % 60 }}分</td>

            <td>{{ floor($workMinutes / 60) }}時間{{ $workMinutes % 60 }}分</td>

            <td>
                <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                    <button>詳細</button>
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>

<div style="text-align: right; margin-top: 20px;">
    <a href="{{ route('admin.staff.csv', ['id' => $staff->id, 'month' => $month]) }}">
        <button>CSV出力</button>
    </a>
</div>


@endsection