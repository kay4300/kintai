@extends('layouts.app')

@section('title', 'kintai')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-detail">

    <h1 class="page-title">勤怠詳細</h1>

    <!-- @php
    $breaks = $attendance->breaks;
    @endphp -->

    @php
    $isPending = $requestData && $requestData->status == 0;
    $isApproved = $requestData && $requestData->status == 1;
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
    <!-- <form method="POST"
        action="{{ route('admin.request.approve', $requestData->id) }}"
        class="detail-card">

        @csrf

        @else -->

    {{-- 修正用フォーム --}}
    <!-- <form method="POST"
            action="{{ route('admin.attendance.update', $attendance->id) }}"
            class="detail-card">

            @csrf
            @method('PUT')

            @endif -->
    @if($isApproveMode)

    {{-- 承認用フォーム --}}
    <form method="POST"
        action="{{ route('admin.request.approve', $requestData->id) }}"
        class="detail-card">
        @csrf

        @else

        {{-- 修正用フォーム（admin / staff 共通） --}}
        <form method="POST"
            action="{{ auth()->guard('admin')->check()
        ? route('admin.attendance.update', $attendance->id)
        : route('staff.attendance.update', $attendance->id) }}"
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
                            value="{{ old('start_time',$isApproveMode
                            ? ($requestData->start_time ? \Carbon\Carbon::parse($requestData->start_time)->format('H:i') : '')
                            : ($attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '')) }}"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>

                        〜

                        <!-- <input type="time" name="end_time"
                        value="{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '' }}"
                        @if($requestData && $requestData->status == 0) readonly @endif> -->
                        <input type="time" name="end_time"
                            value="{{ old('end_time',
        $isApproveMode
            ? ($requestData->end_time ? \Carbon\Carbon::parse($requestData->end_time)->format('H:i') : '')
            : ($attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '')
    ) }}"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>
                        @error('time')
                        <p class="error">{{ $message }}</p>
                        @enderror

                    </td>
                </tr>

                <tr>
                    @php
                    $breaks = $attendance->breakTimes;
                    $break1 = $breaks[0] ?? null;
                    $break2 = $breaks[1] ?? null;
                    @endphp

                    <th>休憩</th>
                    <td>
                        <input type="time" name="break_start_1"
                            value="{{ old('break_start_1',
        $break1?->start_time
            ? \Carbon\Carbon::parse($break1->start_time)->format('H:i')
            : ''
    ) }}"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>

                        〜

                        <input type="time" name="break_end_1"
                            value="{{ old('break_end_1',
        $break1?->end_time
            ? \Carbon\Carbon::parse($break1->end_time)->format('H:i')
            : ''
    ) }}"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>


                        @error('break')
                        <p class="error">{{ $message }}</p>
                        @enderror

                    </td>
                </tr>

                <tr>
                    <th>休憩2</th>
                    <td>
                        <input type="time" name="break_start_2"
                            value="{{ old('break_start_2',
        $break2?->start_time
            ? \Carbon\Carbon::parse($break2->start_time)->format('H:i')
            : ''
    ) }}"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>

                        〜

                        <input type="time" name="break_end_2"
                            value="{{ old('break_end_2',
        $break2?->end_time
            ? \Carbon\Carbon::parse($break2->end_time)->format('H:i')
            : ''
    ) }}"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>

                        @error('break')
                        <p class="error">{{ $message }}</p>
                        @enderror

                    </td>
                </tr>

                <tr>
                    <th>修正理由</th>
                    <td>
                        <textarea name="reason" rows="3"
                            {{ $isApproveMode || $isPending ? 'readonly' : '' }}>
                        {{ $requestData->reason ?? '' }}
                        </textarea>

                        @error('reason')
                        <p class="error">{{ $message }}</p>
                        @enderror

                    </td>
                </tr>
            </table>


            <div class="action-area">

                @if($isApproveMode)

                {{-- 承認モード --}}
                @if($isApproved)
                <button disabled>承認済み</button>
                @else
                <button type="submit" class="approve-btn">承認</button>
                @endif

                @else

                {{-- 通常モード --}}
                @if($isPending)
                <p class="pending-message">承認待ちのため修正はできません。</p>
                @elseif($isApproved)
                <p class="approved-message">承認済み</p>
                @else
                <button type="submit" class="approve-btn">修正</button>
                @endif

                @endif

            </div>

</div>

</form>

</div>
@endsection