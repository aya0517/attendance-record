<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminUserInfoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_users_names_and_emails()
    {
        $admin = Admin::factory()->create();
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/staffs');

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /** @test */
    public function admin_can_view_selected_user_attendance()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/staffs/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    /** @test */
    public function admin_can_navigate_to_previous_month_attendance()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $prevMonth = Carbon::now()->subMonth()->format('Y-m');
        $expectedMonthFormat = now()->subMonth()->format('Y/m');

        $response = $this->actingAs($admin, 'admin')->get("/admin/staffs/{$user->id}?month={$prevMonth}");

        $response->assertStatus(200);
        $response->assertSee($expectedMonthFormat);
    }

    /** @test */
    public function admin_can_navigate_to_next_month_attendance()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $nextMonth = Carbon::now()->addMonth()->format('Y-m');

        $response = $this->actingAs($admin, 'admin')->get("/admin/staffs/{$user->id}?month={$nextMonth}");

        $response->assertStatus(200);
        $expectedDisplayMonth = \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->format('Y/m');
$response->assertSee($expectedDisplayMonth);
    }

    /** @test */
    public function admin_can_view_attendance_detail_when_clicking_detail()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin, 'admin')->get("/admin/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
