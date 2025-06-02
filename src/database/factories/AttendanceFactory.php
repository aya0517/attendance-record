<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $start = Carbon::createFromTime(rand(8, 10), rand(0, 59));
        $end = (clone $start)->addHours(rand(8, 10))->addMinutes(rand(0, 59));
        $breakMinutes = rand(30, 60);

        return [
            'user_id' => User::factory(),
            'date' => now()->format('Y-m-d'),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'status' => 'ended',
            'on_break' => false,
        ];
    }

    public function configure()
{
    return $this->afterCreating(function (Attendance $attendance) {
        $start = Carbon::createFromFormat('H:i', $attendance->start_time);

        $break1Start = $start->copy()->addHours(3);
        $break1End = $break1Start->copy()->addMinutes(rand(15, 30));
        $attendance->breaks()->create([
            'started_at' => $break1Start->format('H:i'),
            'ended_at' => $break1End->format('H:i'),
        ]);

        $break2Start = $break1End->copy()->addHours(2);
        $break2End = $break2Start->copy()->addMinutes(rand(10, 20));
        $attendance->breaks()->create([
            'started_at' => $break2Start->format('H:i'),
            'ended_at' => $break2End->format('H:i'),
        ]);
    });
}

}
