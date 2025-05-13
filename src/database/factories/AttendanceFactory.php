<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        'status' => $this->faker->randomElement(['working', 'on_break', 'ended']),
        'on_break' => $this->faker->boolean(30),
        'created_at' => now(),
        'updated_at' => now(),
    ];
    }
}
