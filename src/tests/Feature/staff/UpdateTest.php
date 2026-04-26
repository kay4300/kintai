<?php

namespace Tests\Feature\staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;
use App\Models\StampCorrectionRequest;

class UpdateTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_出勤時間が退勤時間より後の場合エラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(
            route('staff.attendance.update', $attendance->id),
            [
                'target_date' => '2024-01-01',
                'start_time' => '18:00',
                'end_time' => '09:00',
                'reason' => 'テスト',
            ]
        );

        $response->assertSessionHasErrors(['time']);
    }

    public function test_休憩開始が退勤時間より後の場合エラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(
            route('staff.attendance.update', $attendance->id),
            [
                'target_date' => '2024-01-01',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_start_1' => '19:00',
                'break_end_1' => '19:30',
                'reason' => 'テスト',
            ]
        );

        $response->assertSessionHasErrors(['break']);
    }

    public function test_休憩終了が退勤時間より後の場合エラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(
            route('staff.attendance.update', $attendance->id),
            [
                'target_date' => '2024-01-01',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'break_start_1' => '17:00',
                'break_end_1' => '19:00',
                'reason' => 'テスト',
            ]
        );

        $response->assertSessionHasErrors(['break']);
    }

    public function test_備考未入力でエラー()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(
            route('staff.attendance.update', $attendance->id),
            [
                'target_date' => '2024-01-01',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'reason' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'reason'
        ]);
    }

    public function test_修正申請が作成される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response =$this->actingAs($user)->put(
            route('staff.attendance.update', $attendance->id),
            [
                'target_date' => '2024-01-01',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'reason' => '修正理由',
            ]
        );
        //まずバリデーション通ってるか確認
        $response->assertSessionHasNoErrors();

        $response->assertRedirect(route('staff.attendance.list'));

        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 0,
        ]);
    
        $this->assertDatabaseCount('stamp_correction_requests', 1);
    }

    public function test_承認待ちがあると修正できない()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => '2024-01-01',
            'status' => 0,
            'reason' => '修正理由',
        ]);

        $response = $this->actingAs($user)->put(
            route('staff.attendance.update', $attendance->id),
            [
                'target_date' => '2024-01-01',
                'start_time' => '09:00',
                'end_time' => '18:00',
                'reason' => '修正理由',
            ]
        );
        //バリデーション通ってるか確認
        $response->assertSessionHasNoErrors();
        //本来のテスト
        $response->assertSessionHas('error');
    }
}
