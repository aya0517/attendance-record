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

        $this->post('/attendance/punch', ['action' => 'break_start']);
        $this->post('/attendance/punch', ['action' => 'break_end']);

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

        $this->post('/attendance/punch', ['action' => 'break_end']);

        $this->post('/attendance/punch', ['action' => 'break_start']);
        $this->post('/attendance/punch', ['action' => 'break_end']);

        $attendance->refresh();
        $this->assertFalse((bool) $attendance->on_break);
    }

    /** @test */
public function test_break_times_appear_in_attendance_list()
{
    Carbon::setTestNow(Carbon::create(2025, 6, 3, 10, 0));
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/attendance/punch', ['action' => 'start']);
    $this->post('/attendance/punch', ['action' => 'break_start']);

    Carbon::setTestNow(Carbon::create(2025, 6, 3, 10, 30));
    $this->post('/attendance/punch', ['action' => 'break_end']);

    Carbon::setTestNow(Carbon::create(2025, 6, 3, 18, 0));
    $this->post('/attendance/punch', ['action' => 'end']);

    $response = $this->get('/attendance/list');
    $response->assertStatus(200);

    $response->assertSee('10:00');
    $response->assertSee('18:00');
    $response->assertSee('00:30');
    $response->assertSee('07:30');
}

}
