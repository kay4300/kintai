@extends('layouts.app')

@section('content')
    @include('shared.attendance_detail', ['attendance'=> $attendance, 'isPending' => $isPending])
@endsection