<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceDatetimeDisplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_current_datetime_on_attendance_screen()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $expectedDate = Carbon::now()->format('Y年n月j日');
        $expectedTime = Carbon::now()->format('H:i');

        $response = $this->get('/attendance');

        $response->assertStatus(200);

        $response->assertSee($expectedDate);
        $response->assertSee($expectedTime);
    }
}
