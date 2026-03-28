@extends('layouts.app')

@section('title', 'kintai')

@section('content')

<p>{{ now()->format('Y年m月d日 H:i') }}</p>

@if(!$attendance)
<form method="POST" action="/attendance/start">
    @csrf
    <button>出勤</button>
</form>
@endif

@if($attendance && $attendance->status === 0)
出勤ボタン
@endif

@if($attendance->status === 1)
休憩入ボタン
退勤ボタン
@endif

@if($attendance->status === 2)
休憩戻ボタン
@endif

@if($attendance->status === 3)
お疲れ様でした
@endif

@endsection