@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    <h1 class="page-title">勤怠詳細</h1>

    @php
    $breaks = $attendance->breaks;
    @endphp

    

    {{-- ===================== --}}
    {{-- スタッフ画面 --}}
    {{-- ===================== --}}

    <!-- <form method="POST" action="{{ route('stamp_correction_request.store') }}" class="detail-card"> -->
    <!-- <form method="POST"
        action="{{ $isApproveMode
        ? route('admin.request.approve', $requestData->id)
        : route('admin.attendance.update', $attendance->id) }}"
        class="detail-card"> -->

    @if($isApproveMode)

    {{-- 承認用フォーム --}}
    <form method="POST"
        action="{{ route('admin.request.approve', $requestData->id) }}"
        class="detail-card">

        @csrf

        @else

        {{-- 修正用フォーム --}}
        <form method="POST"
            action="{{ route('admin.attendance.update', $attendance->id) }}"
            class="detail-card">

            @csrf
            @method('PUT')

            @endif

            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
            <input type="hidden" name="target_date" value="{{ $attendance->date }}">

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
                        <!-- <input type="time" name="start_time"
                        value="{{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="start_time"
                            value="{{ $isApproveMode
        ? ($requestData->start_time ? \Carbon\Carbon::parse($requestData->start_time)->format('H:i') : '')
        : ($attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '') }}"
                            {{ $requestData ? 'readonly' : '' }}>


                        〜

                        <!-- <input type="time" name="end_time"
                        value="{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="end_time"
                            value="{{ $isApproveMode
        ? ($requestData->end_time ? \Carbon\Carbon::parse($requestData->end_time)->format('H:i') : '')
        : ($attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '') }}"
                            {{ $requestData ? 'readonly' : '' }}>
                    </td>
                </tr>

                <tr>
                    @php
                    $break1 = $breaks[0] ?? null;
                    $break2 = $breaks[1] ?? null;
                    @endphp

                    <th>休憩</th>
                    <td>
                        <!-- <input type="time" name="break_start_1"
                        value="{{ $break1?->start_time ? \Carbon\Carbon::parse($break1->start_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="break_start_1"
                            value="{{ $isApproveMode
        ? ($requestData->break_start_1
            ? \Carbon\Carbon::parse($requestData->break_start_1)->format('H:i')
            : '')
        : ($attendance->break_start_1
            ? \Carbon\Carbon::parse($attendance->break_start_1)->format('H:i')
            : '') }}"
                            {{ $isApproveMode ? 'readonly' : '' }}>


                        〜

                        <!-- <input type="time" name="break_end_1"
                        value="{{ $break1?->end_time ? \Carbon\Carbon::parse($break1->end_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="break_end_1"
                            value="{{ $isApproveMode
        ? ($requestData->break_end_1
            ? \Carbon\Carbon::parse($requestData->break_end_1)->format('H:i')
            : '')
        : ($attendance->break_end_1
            ? \Carbon\Carbon::parse($attendance->break_end_1)->format('H:i')
            : '') }}"
                            {{ $isApproveMode ? 'readonly' : '' }}>

                    </td>
                </tr>

                <tr>
                    <th>休憩2</th>
                    <td>
                        <!-- <input type="time" name="break_start_2"
                        value="{{ $break2?->start_time ? \Carbon\Carbon::parse($break2->start_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="break_start_2"
                            value="{{ $isApproveMode
        ? ($requestData->break_start_2
            ? \Carbon\Carbon::parse($requestData->break_start_2)->format('H:i')
            : '')
        : ($attendance->break_start_2
            ? \Carbon\Carbon::parse($attendance->break_start_2)->format('H:i')
            : '') }}"
                            {{ $isApproveMode ? 'readonly' : '' }}>


                        〜

                        <!-- <input type="time" name="break_end_2"
                        value="{{ $break2?->end_time ? \Carbon\Carbon::parse($break2->end_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="break_end_2"
                            value="{{ $isApproveMode
        ? ($requestData->break_end_2
            ? \Carbon\Carbon::parse($requestData->break_end_2)->format('H:i')
            : '')
        : ($attendance->break_end_2
            ? \Carbon\Carbon::parse($attendance->break_end_2)->format('H:i')
            : '') }}"
                            {{ $isApproveMode ? 'readonly' : '' }}>

                    </td>
                </tr>

                <tr>
                    <th>修正理由</th>
                    <td>
                        <textarea name="reason" rows="3"
                            {{ $requestData ? 'readonly' : '' }}>
                        {{ $requestData->reason ?? '' }}
                        </textarea>
                    </td>
                </tr>
            </table>

            
            <div class="action-area">

                @if($isApproveMode)

                {{-- 承認モード --}}
                @if($requestData?->status == 1)
                <button disabled>承認済み</button>
                @else
                <button type="submit" class="approve-btn">承認</button>
                @endif

                @else

                {{-- 通常モード --}}
                @if($requestData && $requestData->status == 0)
                <p class="pending-message">承認待ちのため修正はできません。</p>
                @elseif($requestData && $requestData->status == 1)
                <p class="approved-message">承認済み</p>
                @else
                <button type="submit" class="approve-btn">修正</button>
                @endif

                @endif

            </div>

        </form>

</div>
@endsection