@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staffindex.css') }}">
@endsection

@section('content')

<h2>{{ $staff->name }} さんの勤怠一覧</h2>

<table>
    <tr>
        <th>日付</th>
        <th>出勤</th>
        <th>退勤</th>
    </tr>

    @foreach ($attendances as $attendance)
    <tr>
        <td>{{ $attendance->date }}</td>
        <td>{{ $attendance->start_time }}</td>
        <td>{{ $attendance->end_time }}</td>
    </tr>
    @endforeach
</table>
@endsection