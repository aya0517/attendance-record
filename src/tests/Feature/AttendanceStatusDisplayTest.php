<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceStatusDisplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_status_as_off_when_user_is_off_duty()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /** @test */
    public function it_shows_status_as_working()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 'working',
            'on_break' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務中');
    }

    /** @test */
    public function it_shows_status_as_on_break()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),  // ← 今日
            'status' => 'working',
            'on_break' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /** @test */
    public function it_shows_status_as_ended()
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
        $response->assertSee('退勤済');
    }
}
