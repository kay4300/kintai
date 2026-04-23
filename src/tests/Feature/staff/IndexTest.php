<?php

namespace Tests\Feature\staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;

class IndexTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    
    /** @test */
    public function 自分の勤怠情報が全て表示される()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);

        // 自分の勤怠（2日分）
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today()->subDay(),
            'start_time' => '10:00:00',
            'end_time' => '19:00:00',
        ]);

        // 他人の勤怠
        Attendance::factory()->create([
            'user_id' => $otherUser->id,
            'date' => today(),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        $response = $this->get(route('staff.attendance.list'));

        $response->assertStatus(200);

        // 自分のデータは表示される
        $response->assertSee('09:00');
        $response->assertSee('10:00');

        // 他人のデータは表示されない
        $response->assertDontSee('08:00');
    }

    /** @test */
    public function 前月の情報が表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // 先月のデータ
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->subMonth()->startOfMonth(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $month = now()->subMonth()->format('Y-m');

        $response = $this->get('/attendance/list?month=' . $month);

        $response->assertSee('09:00');
    }

    /** @test */
    public function 翌月の情報が表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // 翌月のデータ
        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => now()->addMonth()->startOfMonth(),
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $month = now()->addMonth()->format('Y-m');

        $response = $this->get('/attendance/list?month=' . $month);

        $response->assertSee('09:00');
    }

    /** @test */
    public function 現在の月が表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('staff.attendance.list'));

        $response->assertStatus(200);

        $response->assertSee(now()->format('Y年m月'));
    }

    /** @test */
    public function 詳細画面に遷移できる()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
        ]);

        $response = $this->get(route('staff.attendance.detail', $attendance->id));

        $response->assertStatus(200);

        // 詳細リンクがあるか確認
        $response->assertSee(
            route('staff.attendance.detail', $attendance->id)
        );
    }
}
