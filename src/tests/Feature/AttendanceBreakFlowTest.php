<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class AttendanceBreakFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_user_to_start_break_and_status_changes_to_on_break()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 'working',
            'on_break' => false,
        ]);

        $this->actingAs($user);

        $response = $this->post('/attendance/punch', ['action' => 'break_start']);
        $response->assertRedirect('/attendance');

        $attendance = Attendance::first();
        $this->assertTrue((bool) $attendance->on_break);
    }

    /** @test */
    public function user_can_start_break_multiple_times_per_day()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 'working',
            'on_break' => false,
        ]);

        $this->actingAs($user);

        // 1回目休憩
        $this->post('/attendance/punch', ['action' => 'break_start']);
        $this->post('/attendance/punch', ['action' => 'break_end']);

        // 2回目休憩
        $this->post('/attendance/punch', ['action' => 'break_start']);
        $attendance->refresh();
        $this->assertTrue((bool) $attendance->on_break);
    }

    /** @test */
    public function it_allows_user_to_end_break_and_status_changes_to_working()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 'working',
            'on_break' => true,
        ]);

        $this->actingAs($user);

        $this->post('/attendance/punch', ['action' => 'break_end']);
        $attendance = Attendance::first();
        $this->assertFalse((bool) $attendance->on_break);
    }

    /** @test */
    public function user_can_end_break_multiple_times_per_day()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 'working',
            'on_break' => true,
        ]);

        $this->actingAs($user);

        // 1回目休憩戻り
        $this->post('/attendance/punch', ['action' => 'break_end']);

        // 2回目休憩開始 → 戻り
        $this->post('/attendance/punch', ['action' => 'break_start']);
        $this->post('/attendance/punch', ['action' => 'break_end']);

        $attendance->refresh();
        $this->assertFalse((bool) $attendance->on_break);
    }

    /** @test */
    public function break_times_appear_in_attendance_list()
{
    $user = User::factory()->create();

    $date = Carbon::parse('2025-06-01');

    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
        'date' => $date,
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
        'status' => 'ended',
        'on_break' => false,
    ]);

    $attendance->breaks()->createMany([
        [
            'attendance_id' => $attendance->id,
            'started_at' => $date->copy()->setTime(10, 0, 0)->format('Y-m-d H:i:s'),
            'ended_at'   => $date->copy()->setTime(10, 30, 0)->format('Y-m-d H:i:s'),
        ],
        [
            'attendance_id' => $attendance->id,
            'started_at' => $date->copy()->setTime(14, 0, 0)->format('Y-m-d H:i:s'),
            'ended_at'   => $date->copy()->setTime(14, 30, 0)->format('Y-m-d H:i:s'),
        ],
    ]);

    $this->actingAs($user);

    $response = $this->get('/attendance/list');
    $response->assertStatus(200);
    $response->assertSee('01:00');
}

}
