<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function 出勤できる()
    {
        // dbにダミーデータなくてもここで作成
        $user = \App\Models\User::factory()->create();
        // 
        $this->actingAs($user);

        $response = $this->post('/attendance/start');

        $response->assertStatus(302);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 1, // 出勤中
        ]);
    }

    /** @test */
    public function 退勤できる()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        // 出勤済みデータを作る
        \App\Models\Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 1, // 出勤中
        ]);

        // 退勤処理
        $response = $this->post('/attendance/end');

        $response->assertStatus(302);

        // DB確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 3, // 退勤済
        ]);

        // end_time確認。退勤時間が入っているか。
        $attendance = \App\Models\Attendance::first();
        $this->assertNotNull($attendance->end_time);
    }

    /** @test */
    public function 休憩開始できる()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        // 出勤済み
        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 1, // 出勤中
        ]);

        // 休憩開始
        $response = $this->post('/break/start');

        $response->assertStatus(302);

        // break_timesにデータができているか。休憩時間はbreak_timesテーブルに保存されているため。
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
        ]);

        // ステータスが休憩中になっているか
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 2,
        ]);
    }

    /** @test */
    public function 休憩終了できる()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        // 出勤中
        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 2, // 休憩中
        ]);

        // 休憩データ（開始済み・未終了）
        $break = \App\Models\BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => now(),
            // 未終了の休憩を作るためend_timeはnull
            'end_time' => null,
        ]);

        // 休憩終了
        $response = $this->post('/break/end');

        $response->assertStatus(302);

        // break_timesのend_timeが入っているか
        $this->assertNotNull(
            \App\Models\BreakTime::first()->end_time
        );

        // ステータスが出勤中に戻る
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 1,
        ]);
    }

    /** @test */
    public function 休憩は複数回できる()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        // 出勤中
        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'start_time' => now(),
            'status' => 1,
        ]);

        // 1回目の休憩
        $this->post('/break/start');
        $this->post('/break/end');

        // 2回目の休憩
        $this->post('/break/start');
        $this->post('/break/end');

        // break_times が2件あるか
        $this->assertEquals(
            2,
            \App\Models\BreakTime::where('attendance_id', $attendance->id)->count()
        );
    }

    /** @test */
    public function 出勤から退勤まで一連の流れができる()
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user);

        // ① 出勤
        $this->post('/attendance/start');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'status' => 1,
        ]);

        $attendance = \App\Models\Attendance::where('user_id', $user->id)->first();

        // ② 休憩開始
        $this->post('/break/start');

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 2,
        ]);

        // ③ 休憩終了
        $this->post('/break/end');

        $this->assertDatabaseMissing('break_times', [
            'attendance_id' => $attendance->id,
            'end_time' => null,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 1,
        ]);

        // ④ 退勤
        $this->post('/attendance/end');

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 3,
        ]);

        // 最終確認
        $attendance->refresh();
        $this->assertNotNull($attendance->start_time);
        $this->assertNotNull($attendance->end_time);
    }
}



