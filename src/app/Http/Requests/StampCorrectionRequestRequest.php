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
            'note' => ['required'],
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
            'note.required' => '備考を入力してください。',
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

            foreach ($this->input('breaks', []) as $i => $break) {
                $breakStart = isset($break['started_at']) ? strtotime($break['started_at']) : false;
                $breakEnd = isset($break['ended_at']) ? strtotime($break['ended_at']) : false;

                if ($breakStart !== false && $start !== false && $end !== false) {
                    if ($breakStart < $start || $breakStart > $end) {
                        $validator->errors()->add("breaks.$i.started_at", '出勤時間または退勤時間が不適切な値です');
                    }
                }

                if ($breakEnd !== false && $start !== false && $end !== false) {
                    if ($breakEnd < $start || $breakEnd > $end) {
                        $validator->errors()->add("breaks.$i.ended_at", '出勤時間または退勤時間が不適切な値です');
                    }
                }

                if ($breakStart !== false && $breakEnd !== false &&     $breakStart >= $breakEnd) {
                    $validator->errors()->add("breaks.$i.started_at", '出勤時間または退勤時間が不適切な値です');
                }
            }
        });
    }
}
