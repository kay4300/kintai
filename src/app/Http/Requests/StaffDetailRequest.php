<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StaffDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start_1' => 'nullable|date_format:H:i',
            'break_end_1' => 'nullable|date_format:H:i',
            'break_start_2' => 'nullable|date_format:H:i',
            'break_end_2' => 'nullable|date_format:H:i',
            'reason' => 'required|string',
            //
        ];
    }

    public function messages()
    {
        return [
            'reason.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $start = Carbon::parse($this->start_time);
            $end = Carbon::parse($this->end_time);

            // ① 出勤・退勤チェック
            if ($start >= $end) {
                $validator->errors()->add('time', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩1
            if ($this->break_start_1 && $this->break_end_1) {

                $bStart = Carbon::parse($this->break_start_1);
                $bEnd   = Carbon::parse($this->break_end_1);

                // ② 開始が勤務外
                if ($bStart < $start || $bStart > $end) {
                    $validator->errors()->add('break', '休憩時間が不適切な値です');
                }

                // ③ 終了が退勤後
                if ($bEnd > $end) {
                    $validator->errors()->add('break', '休憩時間もしくは退勤時間が不適切な値です');
                }

                // 前後関係
                if ($bStart >= $bEnd) {
                    $validator->errors()->add('break', '休憩時間が不適切な値です');
                }
            }

            // 休憩2
            if ($this->break_start_2 && $this->break_end_2) {

                $bStart = Carbon::parse($this->break_start_2);
                $bEnd   = Carbon::parse($this->break_end_2);

                if ($bStart < $start || $bStart > $end) {
                    $validator->errors()->add('break', '休憩時間が不適切な値です');
                }

                if ($bEnd > $end) {
                    $validator->errors()->add('break', '休憩時間もしくは退勤時間が不適切な値です');
                }

                if ($bStart >= $bEnd) {
                    $validator->errors()->add('break', '休憩時間が不適切な値です');
                }
            }
            
        });
    }
}
