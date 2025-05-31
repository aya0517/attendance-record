<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
{
    User::factory()->count(5)->create([
        'is_admin' => false,
    ])->each(function ($user) {
        $startDate = Carbon::now()->subMonths(4)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $date = $startDate->copy();
        while ($date->lte($endDate)) {
            if (!in_array($date->dayOfWeekIso, [6, 7])) {
                Attendance::factory()->create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),
                ]);
            }
            $date->addDay();
        }
    });
}
}
