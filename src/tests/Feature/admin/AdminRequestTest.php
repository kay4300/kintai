<?php

namespace Tests\Feature\admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\AdminUser;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;

class AdminRequestTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_承認待ちの修正申請が全て表示される()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        // 承認待ち
        $pending = StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => 'テスト申請1',
            'status' => 0,
        ]);

        // 承認済み
        $approved = StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => 'テスト申請2',
            'status' => 1,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.index', ['status' => 'pending']));

        $response->assertStatus(200);

        $response->assertSee('テスト申請1');
        $response->assertDontSee('テスト申請2');
    }
    public function test_承認済みの修正申請が全て表示される()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $pending = StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => 'テスト1',
            'status' => 0,
        ]);

        $approved = StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => 'テスト2',
            'status' => 1,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.index', ['status' => 'approved']));

        $response->assertStatus(200);

        $response->assertSee('テスト2');
        $response->assertDontSee('テスト1');
    }

    public function test_修正申請の詳細内容が正しく表示される()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $requestData = StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => '詳細表示テスト',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.show', $requestData->id));

        $response->assertStatus(200);

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_修正申請の承認処理が正しく行われる()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-04-10',
        ]);

        $requestData = StampCorrectionRequest::factory()->create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => $attendance->date,
            'reason' => '承認テスト',
            'status' => 0,
            'start_time' => '10:00',
            'end_time' => '19:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.request.approve', $requestData->id));

        $response->assertRedirect(route('admin.request.index'));

        // ステータス更新確認
        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $requestData->id,
            'status' => 1,
        ]);

        // 勤怠更新確認（datetimeになっている）
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'start_time' => '2024-04-10 10:00:00',
            'end_time' => '2024-04-10 19:00:00',
        ]);
    }
}
