@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@yield('css')
@endsection

@section('content')

@if ($attendance->status === 'pending')
<p>承認待ちのため修正ができません。</p>
@else
<form method="POST" action="">
    @csrf

    出勤
    <input type="time" name="start_time" value="{{ $attendance->start_time }}">

    退勤
    <input type="time" name="end_time" value="{{ $attendance->end_time }}">

    @foreach ($attendance->breakTimes as $break)
    休憩
    <input type="time" name="break_start[]" value="{{ $break->start_time }}">
    <input type="time" name="break_end[]" value="{{ $break->end_time }}">
    @endforeach

    備考
    <textarea name="note">{{ $attendance->note }}</textarea>

    <button type="submit">修正</button>
</form>
@endif
@endsection