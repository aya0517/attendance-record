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
            $breakStart = Carbon::createFromFormat('H:i', $attendance->start_time)->addHours(3);
            $breakEnd = (clone $breakStart)->addMinutes(rand(15, 45));

            $attendance->breaks()->create([
                'started_at' => $breakStart->format('H:i'),
                'ended_at' => $breakEnd->format('H:i'),
            ]);
        });
    }
}
