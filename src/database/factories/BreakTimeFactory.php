<?php

namespace Database\Factories;

use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        $start = Carbon::createFromTime(10, 0, 0);
        $end = (clone $start)->addMinutes(30);

        return [
            'attendance_id' => Attendance::factory(),
            'started_at' => $start,
            'ended_at' => $end,
        ];
    }
}
