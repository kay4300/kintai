@extends('layouts.app')

@section('content')
<div class="container">

    <h2>申請一覧</h2>

    {{-- タブ --}}
    <a href="{{ route('...') }}"
        style="{{ $status === 'pending' ? 'font-weight:bold;' : '' }}">
        承認待ち
    </a>

    <a href="{{ route('applications.index', ['status' => 'approved']) }}"
        @if($status==='approved' ) style="margin-left: 20px; font-weight:bold;"
        @else style="margin-left: 20px;"
        @endif>
        承認済み
    </a>
</div>

{{-- 一覧テーブル --}}
<table border="1" width="100%" cellpadding="10">
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
        @forelse($applications as $app)
        <tr>
            <td>
                {{ $app->status === 1 ? '承認待ち' : '承認済み' }}
            </td>
            <td>{{ $app->user->name }}</td>
            <td>{{ \Carbon\Carbon::parse($app->target_date)->format('Y/m/d') }}</td>
            <td>{{ $app->created_at->format('Y/m/d') }}</td>
            <td>{{ $app->reason }}</td>
            <td>
                <a href="{{ route('applications.show', $app->id) }}">詳細</a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6">データがありません</td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>
@endsection