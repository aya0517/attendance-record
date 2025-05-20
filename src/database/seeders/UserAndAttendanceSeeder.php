<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        User::factory(10)->create()->each(function ($user) {
            Attendance::factory()->count(5)->create([
                'user_id' => $user->id,
            ]);
        });
    }

}
