@extends('layouts.app')

@section ('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/staff/detail.css') }}">
@endsection

@section('content')
@include('shared.attendance_detail', ['attendance'=> $attendance, 'isPending' => $isPending])
@endsection