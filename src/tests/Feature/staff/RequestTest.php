<?php

namespace Tests\Feature\staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
Use App\Models\User;
use App\Models\StampCorrectionRequest;

class RequestTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    
    use RefreshDatabase;

    public function test_承認待ちに自分の申請が表示される()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        // 自分（承認待ち）
        StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => '2024-01-01',
            'reason' => '修正理由',
            'status' => 0,
        ]);

        // 他人（表示されない）
        $otherUser = User::factory()->create();
        $otherAttendance = Attendance::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        StampCorrectionRequest::create([
            'user_id' => $otherUser->id,
            'attendance_id' => $otherAttendance->id,
            'target_date' => '2024-01-01',
            'reason' => '他人の修正理由',
            'status' => 0,
        ]);

        $response = $this->actingAs($user)->get(
            route('stamp_correction_request.list')
        );

        $response->assertStatus(200);

        $response->assertSee('修正理由');
        $response->assertDontSee('他人の修正理由');
    }

    public function test_承認済みに承認済みデータが表示される()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => '2024-01-01',
            'reason' => '承認済みデータ',
            'status' => 1,
        ]);

        $response = $this->actingAs($user)->get(
            route('stamp_correction_request.list', [
                'status' => 'approved' // ← これ必須！！
            ])
        );

        $response->assertStatus(200);

        $response->assertSee('承認済みデータ');
    }

    public function test_詳細ボタンで勤怠詳細画面に遷移する()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
        ]);

        $request = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'target_date' => '2024-01-01',
            'reason' => '修正理由',
            'status' => 0,
        ]);

        $response = $this->actingAs($user)->get(
            route('staff.attendance.detail', [
                'id' => $attendance->id,
                'requestId' => $request->id
            ])
        );

        $response->assertStatus(200);
    }
}
