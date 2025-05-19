<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'break_start' => [
                'required',
                'date_format:H:i',
                'after_or_equal:start_time',
                'before:end_time'
            ],
            'break_end' => [
                'required',
                'date_format:H:i',
                'after_or_equal:start_time',
                'before:end_time'
            ],
            'note' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間は必須です。',
            'start_time.before' => '出勤時間は退勤時間より前である必要があります。',
            'end_time.required' => '退勤時間は必須です。',
            'end_time.after' => '退勤時間は出勤時間より後である必要があります。',
            'break_start.required' => '休憩開始時間は必須です。',
            'break_start.after_or_equal' => '休憩開始は出勤時間以降である必要があります。',
            'break_start.before' => '休憩開始は退勤時間より前である必要があります。',
            'break_end.required' => '休憩終了時間は必須です。',
            'break_end.after_or_equal' => '休憩終了は出勤時間以降である必要があります。',
            'break_end.before' => '休憩終了は退勤時間より前である必要があります。',
            'note.required' => '備考は必須です。',
        ];
    }
}
