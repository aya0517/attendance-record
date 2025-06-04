<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_attendance_records_for_the_logged_in_user()
    {
        $user = User::factory()->create();
        Attendance::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        foreach (Attendance::all() as $attendance) {
            $response->assertSee(Carbon::parse($attendance->date)->format('m/d'));
        }
    }

    /** @test */
    public function it_displays_current_month_by_default()
    {
        $user = User::factory()->create();
        $today = Carbon::create(2025, 6, 3);
        Carbon::setTestNow($today);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $today->format('Y-m-d'),
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('2025/06');
    }

    /** @test */
    public function it_displays_previous_month_data_when_prev_button_clicked()
    {
        $user = User::factory()->create();
        $may = Carbon::create(2025, 5, 15);
        Carbon::setTestNow($may);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $may->format('Y-m-d'),
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list?month=2025-05');

        $response->assertStatus(200);
        $response->assertSee('2025/05');
    }

    /** @test */
    public function it_displays_next_month_data_when_next_button_clicked()
    {
        $user = User::factory()->create();
        $july = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($july);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $july->format('Y-m-d'),
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list?month=2025-07');

        $response->assertStatus(200);
        $response->assertSee('2025/07');
    }

    /** @test */
    public function it_redirects_to_detail_page_when_clicking_detail_button()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(route('attendance.detail', ['id' => $attendance->id]));
    }
}
