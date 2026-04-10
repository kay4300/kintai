@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    <h1 class="page-title">勤怠詳細</h1>

    {{-- ===================== --}}
    {{-- 承認モード --}}
    {{-- ===================== --}}
    @if(isset($isApproveMode) && $isApproveMode)

    <form method="POST" action="{{ route('admin.stamp_correction_request.approve.update', $requestData->id) }}" class="detail-card">
        @csrf

        @php
        $breaks = $attendance->breaks;
        @endphp

        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                    {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
                </td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time"
                        value="{{ $requestData->start_time ? \Carbon\Carbon::parse($requestData->start_time)->format('H:i') : '' }}"
                        disabled>
                    〜
                    <input type="time"
                        value="{{ $requestData->end_time ? \Carbon\Carbon::parse($requestData->end_time)->format('H:i') : '' }}"
                        disabled>
                </td>
            </tr>

            <tr>
                <th>休憩</th>
                <td>
                    <input type="time"
                        value="{{ isset($breaks[0]) && $breaks[0]->start_time ? \Carbon\Carbon::parse($breaks[0]->start_time)->format('H:i') : '' }}"
                        disabled>
                    〜
                    <input type="time"
                        value="{{ isset($breaks[0]) && $breaks[0]->end_time ? \Carbon\Carbon::parse($breaks[0]->end_time)->format('H:i') : '' }}"
                        disabled>
                </td>
            </tr>

            <tr>
                <th>休憩2</th>
                <td>
                    <input type="time"
                        value="{{ isset($breaks[1]) && $breaks[1]->start_time ? \Carbon\Carbon::parse($breaks[1]->start_time)->format('H:i') : '' }}"
                        disabled>
                    〜
                    <input type="time"
                        value="{{ isset($breaks[1]) && $breaks[1]->end_time ? \Carbon\Carbon::parse($breaks[1]->end_time)->format('H:i') : '' }}"
                        disabled>
                </td>
            </tr>

            <tr>
                <th>修正理由</th>
                <td>
                    <textarea rows="3" disabled>{{ $requestData->reason }}</textarea>
                </td>
            </tr>
        </table>

        <div class="action-area">
            @if($requestData->status == 1)
            <button class="approve-btn" disabled>承認済み</button>
            @else
            <button type="submit" class="approve-btn">承認</button>
            @endif
        </div>

    </form>

    {{-- ===================== --}}
    {{-- 通常 / 修正モード --}}
    {{-- ===================== --}}
    @else

    @if (request()->is('admin/*'))
    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}" class="detail-card">
        @method('PUT')
        @else
        <form method="POST" action="{{ route('stamp_correction_request.store') }}" class="detail-card">
            @endif

            @csrf
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
            <input type="hidden" name="target_date" value="{{ $attendance->date }}">

            @php
            $breaks = $attendance->breaks;
            @endphp

            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>
                        {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                        {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
                    </td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="start_time"
                            value="{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}"
                            @if(isset($isPending) && $isPending) disabled @endif>
                        〜
                        <input type="time" name="end_time"
                            value="{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}"
                            @if(isset($isPending) && $isPending) disabled @endif>
                    </td>
                </tr>

                <tr>
                    <th>休憩</th>
                    <td>
                        <input type="time" name="break_start_1"
                            value="{{ isset($breaks[0]) && $breaks[0]->start_time ? \Carbon\Carbon::parse($breaks[0]->start_time)->format('H:i') : '' }}"
                            @if(isset($isPending) && $isPending) disabled @endif>
                        〜
                        <input type="time" name="break_end_1"
                            value="{{ isset($breaks[0]) && $breaks[0]->end_time ? \Carbon\Carbon::parse($breaks[0]->end_time)->format('H:i') : '' }}"
                            @if(isset($isPending) && $isPending) disabled @endif>
                    </td>
                </tr>

                <tr>
                    <th>休憩2</th>
                    <td>
                        <input type="time" name="break_start_2"
                            value="{{ isset($breaks[1]) && $breaks[1]->start_time ? \Carbon\Carbon::parse($breaks[1]->start_time)->format('H:i') : '' }}"
                            @if(isset($isPending) && $isPending) disabled @endif>
                        〜
                        <input type="time" name="break_end_2"
                            value="{{ isset($breaks[1]) && $breaks[1]->end_time ? \Carbon\Carbon::parse($breaks[1]->end_time)->format('H:i') : '' }}"
                            @if(isset($isPending) && $isPending) disabled @endif>
                    </td>
                </tr>

                <tr>
                    <th>修正理由</th>
                    <td>
                        @if(isset($isPending) && $isPending)
                        <textarea rows="3" disabled>{{ $requestData->reason ?? '' }}</textarea>
                        @else
                        <textarea name="reason" rows="3" required></textarea>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="action-area">
                @if(isset($isPending) && $isPending)
                <p class="pending-message">承認待ちのため修正できません</p>
                @else
                <button type="submit" class="approve-btn">修正</button>
                @endif
            </div>

        </form>

        @endif

</div>
@endsection