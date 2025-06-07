<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequestRequest extends FormRequest
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
            'breaks.*.started_at' => ['nullable', 'date_format:H:i'],
            'breaks.*.ended_at' => ['nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => '出勤時間は必須です。',
            'end_time.required' => '退勤時間は必須です。',
            'start_time.date_format' => '出勤時間は HH:MM 形式で入力してください。',
            'end_time.date_format' => '退勤時間は HH:MM 形式で入力してください。',
            'breaks.*.started_at.date_format' => '休憩開始時間は HH:MM 形式で入力してください。',
            'breaks.*.ended_at.date_format' => '休憩終了時間は HH:MM 形式で入力してください。',
            'note.string' => '備考は文字列で入力してください。',
        ];
    }

    public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $start = strtotime($this->input('start_time'));
        $end = strtotime($this->input('end_time'));

        $hasInvalidTime = false;

        if ($start !== false && $end !== false && $start >= $end) {
            $hasInvalidTime = true;
        }

        foreach ($this->input('breaks', []) as $i => $break) {
            $breakStart = isset($break['started_at']) ? strtotime($break['started_at']) : false;
            $breakEnd = isset($break['ended_at']) ? strtotime($break['ended_at']) : false;

            if ($breakStart !== false && ($breakStart > $end)) {
                $hasInvalidTime = true;
            }

            if ($breakEnd !== false && ($breakEnd > $end)) {
                $hasInvalidTime = true;
            }
        }

        if ($hasInvalidTime) {
            $validator->errors()->add('start_time', '出勤時間もしくは退勤時間が不適切な値です');
        }

        if (trim($this->input('note', '')) === '') {
            $validator->errors()->add('note', '備考を記入してください');
        }
    });
}

}
