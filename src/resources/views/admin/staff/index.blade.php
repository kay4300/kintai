@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/staffindex.css') }}">
@endsection

@section('content')
<h1>スタッフ一覧</h1>

<table>
    <tr>
        <th>名前</th>
        <th>メールアドレス</th>
        <th>月次勤怠</th>
    </tr>

    @foreach ($staffs as $staff)
    <tr>
        <td>{{ $staff->name }}</td>
        <td>{{ $staff->email }}</td>
        <td>
            <a href="{{ route('admin.staff.attendance', $staff->id) }}">
                詳細
            </a>
        </td>
    </tr>
    @endforeach
</table>
@endsection