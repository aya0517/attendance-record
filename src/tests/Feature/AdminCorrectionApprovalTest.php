<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;

class AdminCorrectionApprovalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_pending_correction_requests()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/requests/index');

        $response->assertStatus(200);
        $response->assertSee('承認待ち');
    }

    /** @test */
    public function shows_approved_correction_requests()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/requests/index');

        $response->assertStatus(200);
        $response->assertSee('承認済み');
    }

    /** @test */
    public function shows_request_detail_correctly()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $request = StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'note' => 'Test 修正申請',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/requests/' . $request->id);

        $response->assertStatus(200);
        $response->assertSee('Test 修正申請');
    }

    /** @test */
    public function approves_correction_request_and_updates_attendance()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '09:00',
            'end_time' => '18:00',
            'status' => 'ended',
        ]);

        $request = StampCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'start_time' => '10:00',
            'end_time' => '19:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/attendance/requests/approve/' . $request->id);

        $response->assertRedirect();
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);
    }
}
