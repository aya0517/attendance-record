<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_error_when_start_time_is_after_end_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create();
        StampCorrectionRequest::factory()->create([
        'attendance_id' => $attendance->id,
        'status' => 'approved',
        ]);

        $this->actingAs($user);

        $response = $this->patch("/attendance/{$attendance->id}", [
            'start_time' => '19:00',
            'end_time' => '09:00',
            'user_id' => $user->id,
            'break_start' => '10:00',
            'break_end' => '10:30',
            'note' => 'Invalid test',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    /** @test */
    public function shows_error_when_break_start_is_outside_work_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch("/attendance/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '08:00', // before work start
            'break_end' => '10:00',
            'note' => 'Break before start',
        ]);

        $response->assertSessionHasErrors(['break_start']);
    }

    /** @test */
    public function shows_error_when_break_end_is_outside_work_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch("/attendance/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '17:00',
            'break_end' => '19:00', // after work end
            'note' => 'Break after end',
        ]);

        $response->assertSessionHasErrors(['break_end']);
    }

    /** @test */
    public function shows_error_when_note_is_empty()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch("/attendance/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors(['note']);
    }

    /** @test */
    public function submits_correction_request_successfully_with_valid_data()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->patch("/attendance/{$attendance->id}", [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'note' => 'Correction requested',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function shows_user_pending_requests_on_list()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        StampCorrectionRequest::factory()->create([
    'attendance_id' => $attendance->id,
    'status' => 'pending',
]);


        $this->actingAs($user);
        $response = $this->get('/stamp_correction_request/list');
        $response->assertSee('承認待ち');
    }

    /** @test */
    public function shows_user_approved_requests_on_list()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);
        StampCorrectionRequest::factory()->create([
    'attendance_id' => $attendance->id,
    'status' => 'pending',
]);


        $this->actingAs($user);
        $response = $this->get('/stamp_correction_request/list');
        $response->assertSee('承認済み');
    }

    /** @test */
    public function navigates_to_attendance_detail_from_list()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');
        $response->assertSee(route('attendance.detail', ['id' => $attendance->id]));
    }
}
