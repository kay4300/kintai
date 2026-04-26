<?php

namespace Tests\Feature\admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\AdminUser;
use App\Models\User;
use App\Models\Attendance;

class AdminDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use refreshDatabase;

    public function test_勤怠詳細画面に正しいデータが表示される()
    {

        $admin = AdminUser::factory()->create();

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-04-24',
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.detail', $attendance->id));

        $response->assertStatus(200);

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_出勤時間が退勤時間より後ならエラー()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '19:00',
                'end_time' => '18:00',
                'reason' => '修正',
            ]);

        $response->assertSessionHasErrors();
    }

    public function test_休憩開始が退勤時間より後ならエラー()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_start_1' => '19:00',
                'reason' => '修正',
            ]);

        $response->assertSessionHasErrors();
    }

    public function test_休憩終了が退勤時間より後ならエラー()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_end_1' => '19:00',
                'reason' => '修正',
            ]);

        $response->assertSessionHasErrors();
    }

    public function test_備考未入力ならエラー()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->put(route('admin.attendance.update', $attendance->id), [
                'start_time' => '09:00',
                'end_time' => '18:00',
                'reason' => '', // 未入力
            ]);

        $response->assertSessionHasErrors('reason');
    }
}
