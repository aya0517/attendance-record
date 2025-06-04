<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;

class AttendancePunchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_punch_button_for_off_status_and_updates_to_working()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤');

        $response = $this->post('/attendance/punch', [
            'action' => 'start',
        ]);
        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => 'working',
            'date' => now()->toDateString(),
        ]);
    }

    /** @test */
    public function it_does_not_show_punch_button_if_user_already_punched()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'ended',
            'on_break' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }

    /** @test */
    public function it_registers_start_time_and_displays_in_admin_screen()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/attendance/punch', [
            'action' => 'start',
        ]);

        $attendance = Attendance::where('user_id', $user->id)->first();

        $this->assertNotNull($attendance);
        $this->assertEquals(now()->toDateString(), $attendance->date);
        $this->assertEquals('working', $attendance->status);

        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee(substr($attendance->start_time, 0, 5));
    }

}
