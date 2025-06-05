<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function shows_correct_attendance_detail_information()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->user->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'note' => 'Regular day'
        ]);

        $this->actingAs($this->admin, 'admin');
        $response = $this->get(route('admin.attendance.detail', $attendance->id));

        $response->assertStatus(200);
        $response->assertSee('Regular day');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function validation_error_when_start_time_is_after_end_time()
    {
        $attendance = Attendance::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin, 'admin');
        $response = $this->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '20:00',
            'end_time' => '08:00',
            'note' => 'test note',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    /** @test */
    public function validation_error_when_break_start_is_after_end_time()
    {
        $attendance = Attendance::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin, 'admin');
        $response = $this->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '18:00',
            'break_start' => '19:00',
            'break_end' => '19:30',
            'note' => 'test note',
        ]);

        $response->assertSessionHasErrors(['break_start']);
    }

    /** @test */
    public function validation_error_when_break_end_is_after_end_time()
    {
        $attendance = Attendance::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin, 'admin');
        $response = $this->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '20:00',
            'note' => 'test note',
        ]);

        $response->assertSessionHasErrors(['break_end']);
    }

    /** @test */
    public function validation_error_when_note_is_empty()
    {
        $attendance = Attendance::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin, 'admin');
        $response = $this->patch(route('admin.attendance.update', $attendance->id), [
            'start_time' => '08:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors(['note']);
    }
}
