<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_users_attendance_for_the_day()
    {
        $admin = Admin::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Attendance::factory()->create(['user_id' => $user1->id, 'date' => today()]);
        Attendance::factory()->create(['user_id' => $user2->id, 'date' => today()]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
    }

    /** @test */
    public function it_displays_today_date_on_initial_view()
    {
        $admin = Admin::factory()->create();

        $today = Carbon::today()->format('Y/m/d');

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($today);
    }

    /** @test */
    public function it_displays_previous_day_data_when_previous_button_is_clicked()
    {
        $admin = Admin::factory()->create();
        $yesterday = Carbon::yesterday();

        Attendance::factory()->create([
            'user_id' => User::factory(),
            'date' => $yesterday
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list?date=' . $yesterday->toDateString());
        $response->assertStatus(200);
        $response->assertSee($yesterday->format('Y/m/d'));
    }

    /** @test */
    public function it_displays_next_day_data_when_next_button_is_clicked()
    {
        $admin = Admin::factory()->create();
        $tomorrow = Carbon::tomorrow();

        Attendance::factory()->create([
            'user_id' => User::factory(),
            'date' => $tomorrow
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list?date=' . $tomorrow->toDateString());
        $response->assertStatus(200);
        $response->assertSee($tomorrow->format('Y/m/d'));
    }
}
