<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceEndTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_user_to_end_work_and_status_changes_to_ended()
    {
        Carbon::setTestNow(Carbon::create(2025, 6, 3, 17, 0));
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/attendance/punch', ['action' => 'start']);

        Carbon::setTestNow(Carbon::create(2025, 6, 3, 18, 0));
        $this->post('/attendance/punch', ['action' => 'end']);

        $this->get('/attendance')
    ->assertSee('退勤済')
    ->assertDontSee('value="end"');

    }

    /** @test */
    public function it_registers_end_time_and_shows_in_admin_detail_screen()
{
    Carbon::setTestNow(Carbon::create(2025, 6, 3, 10, 0,0));

    $user = User::factory()->create();

    $attendance = Attendance::factory()->create([
        'user_id' => $user->id,
        'date' => now()->format('Y-m-d'),
        'start_time' => '10:00',
        'status' => 'working',
        'on_break' => false,
    ]);

    Carbon::setTestNow(Carbon::create(2025, 6, 3, 18,0,0));
    $this->actingAs($user);
    $this->post('/attendance/punch', ['action' => 'end']);

    $updatedAttendance = Attendance::find($attendance->id);
    $endTime = \Carbon\Carbon::parse($updatedAttendance->end_time)->format('H:i');

    $admin = Admin::factory()->create();
    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/attendance/' . $attendance->id);
    $response->assertStatus(200);
    $response->assertSee('10:00');
    $response->assertSee($endTime);
}
}
