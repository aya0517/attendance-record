<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_user_name_on_detail_page()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('Test User');
    }

    /** @test */
    public function it_displays_correct_date_on_detail_page()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-06-03'
        ]);

        $this->actingAs($user);
        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('2025年');
        $response->assertSee('6月3日');
    }

    /** @test */
    public function it_displays_correct_start_and_end_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'start_time' => '09:00',
            'end_time' => '18:00'
        ]);

        $this->actingAs($user);
        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */ public function it_displays_correct_break_time()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()
            ->hasBreaks(0)
            ->create([
                'user_id' => $user->id,
                'date' => '2025-06-03',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
            ]);

        $attendance->breaks()->createMany([
            [
                'started_at' => ('2025-06-03 13:00:00'),
                'ended_at' => ('2025-06-03 13:30:00'),
            ],
            [
                'started_at' => ('2025-06-03 15:00:00'),
                'ended_at' => ('2025-06-03 15:15:00'),
            ]
        ]);

        $this->actingAs($user);
        $response = $this->get("/attendance/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('13:00');
        $response->assertSee('13:30');
        $response->assertSee('15:00');
        $response->assertSee('15:15');
    }
}
