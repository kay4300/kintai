@extends('layouts.app')

@section('content')
<div class="container">

    <h2>申請一覧</h2>

    {{-- タブ --}}
    <a href="{{ route('stamp_correction_request.list', ['status' => 'pending']) }}"
        class="tab {{ $status === 'pending' ? 'active' : '' }}">
        承認待ち
    </a>

    <a href="{{ route('stamp_correction_request.list', ['status' => 'approved']) }}"
        class="tab {{ $status === 'approved' ? 'active' : '' }}">
        承認済み
    </a>
</div>

{{-- 一覧テーブル --}}
<table>
    <thead>
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請日時</th>
            <th>申請理由</th>
            <th>詳細</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requests as $request)
        <tr>
            <td>
                {{ $request->status === 1 ? '承認待ち' : '承認済み' }}
            </td>
            <td>
                {{ $request->user->name ?? '' }}
            </td>
            <td>
                {{ \Carbon\Carbon::parse($request->target_date)->format('Y/m/d H:i') }}
            </td>
            <td>
                {{ $request->created_at->format('Y/m/d H:i') }}
            </td>
            <td>
                {{ $request->reason }}
            </td>
            <td>
                <a href="#">詳細</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


</div>
@endsection