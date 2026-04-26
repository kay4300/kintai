<?php

namespace Tests\Feature\staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class DetailTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    

    public function test_詳細画面が表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $response = $this->get(
            route('staff.attendance.detail', $attendance->id)
        );

        $response->assertStatus(200);
    }

    public function test_他人の勤怠詳細は見れない()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $otherUser->id,
            'date' => today(),
        ]);

        $response = $this->get(
            route('staff.attendance.detail', $attendance->id)
        );

        $response->assertStatus(404);
    }

    public function test_勤怠詳細が表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->get(
            route('staff.attendance.detail', $attendance->id)
        );

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_休憩時間が表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::parse('2026-04-24 12:00:00'),
            'end_time' => Carbon::parse('2026-04-24 13:00:00'),
        ]);

        $response = $this->get(
            route('staff.attendance.detail', $attendance->id)
        );

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
