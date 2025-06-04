<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AttendanceListViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function it_displays_all_attendance_records_for_the_user()
{
    $user = User::factory()->create();

    Attendance::factory()->count(3)->create([
        'user_id' => $user->id,
        'date' => now()->startOfMonth()->addDays(1),
    ]);

    $this->actingAs($user);
    $response = $this->get('/attendance/list');

    $response->assertStatus(200);

    $this->assertEquals(3, substr_count($response->getContent(), '>詳細</a>'));
}


    /** @test */
    public function it_shows_current_month_attendance_on_initial_access()
    {
        Carbon::setTestNow(Carbon::create(2025, 6, 1));
        $user = User::factory()->create();
        Attendance::factory()->create(['user_id' => $user->id, 'date' => '2025-06-01']);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('2025/06');
    }

    /** @test */
    public function it_shows_previous_month_attendance_when_prev_month_is_selected()
    {
        Carbon::setTestNow(Carbon::create(2025, 6, 1));
        $user = User::factory()->create();
        Attendance::factory()->create(['user_id' => $user->id, 'date' => '2025-05-15']);

        $this->actingAs($user);
        $response = $this->get('/attendance/list?month=2025-05');

        $response->assertStatus(200);
        $response->assertSee('2025/05');
    }

    /** @test */
    public function it_shows_next_month_attendance_when_next_month_is_selected()
    {
        Carbon::setTestNow(Carbon::create(2025, 6, 1));
        $user = User::factory()->create();
        Attendance::factory()->create(['user_id' => $user->id, 'date' => '2025-07-01']);

        $this->actingAs($user);
        $response = $this->get('/attendance/list?month=2025-07');

        $response->assertStatus(200);
        $response->assertSee('2025/07');
    }

    /** @test */
    public function it_moves_to_detail_page_when_detail_button_is_clicked()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get('/attendance/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee($attendance->start_time);
    }
}