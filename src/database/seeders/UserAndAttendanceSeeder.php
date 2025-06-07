<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class UserAndAttendanceSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Attendance::truncate();
        User::truncate();
        Admin::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Admin::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'),
        ]);

        $user = User::create([
        'name' => '一般ユーザー',
        'email' => 'user1@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('user1234'),
        'is_admin' => false,
        ]);

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

        User::factory()->count(4)->create([
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
