<?php

namespace Database\Factories;

use App\Models\StampCorrectionRequest;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class StampCorrectionRequestFactory extends Factory
{
    protected $model = StampCorrectionRequest::class;

    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'user_id' => User::factory(),
            'date' => now()->toDateString(),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'break_start' => $this->faker->time('H:i'),
            'break_end' => $this->faker->time('H:i'),
            'note' => $this->faker->sentence(),
            'status' => 'pending',
        ];
    }
}
