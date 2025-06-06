<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'break_start' => ['nullable', 'date_format:H:i'],
            'break_end' => ['nullable', 'date_format:H:i'],
            'note' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間は必須です。',
            'end_time.required' => '退勤時間は必須です。',
            // 'break_start.required' => '休憩開始時間は必須です。',
            // 'break_end.required' => '休憩終了時間は必須です。',
            'note.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = strtotime($this->input('start_time'));
            $end = strtotime($this->input('end_time'));

            if ($start !== false && $end !== false && $start >= $end) {
                $validator->errors()->add('start_time', '出勤時間または退勤時間が不適切な値です');
            }

            $breakStart = strtotime($this->input('break_start'));
            $breakEnd = strtotime($this->input('break_end'));

            if ($breakStart !== false && $start !== false && $end !== false) {
                if ($breakStart < $start || $breakStart > $end) {
                    $validator->errors()->add('break_start', '出勤時間または退勤時間が不適切な値です');
                }
            }

            if ($breakEnd !== false && $start !== false && $end !== false) {
                if ($breakEnd < $start || $breakEnd > $end) {
                    $validator->errors()->add('break_end', '出勤時間または退勤時間が不適切な値です');
                }
            }

            if ($breakStart !== false && $breakEnd !== false && $breakStart >= $breakEnd) {
                $validator->errors()->add('break_start', '出勤時間または退勤時間が不適切な値です');
            }
        });
    }
}
