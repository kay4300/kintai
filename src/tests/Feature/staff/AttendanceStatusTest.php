<?php

namespace Tests\Feature\staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;

class AttendanceStatusTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    
    
    public function test_勤務外のステータスが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('staff.attendance.index'));

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    
    public function test_出勤中のステータスが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('staff.attendance.index'));

        $response->assertSee('出勤中');
    }

    
    public function test_休憩中のステータスが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 2,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('staff.attendance.index'));

        $response->assertSee('休憩中');
    }

    
    public function test_退勤済のステータスが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 3,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('staff.attendance.index'));

        $response->assertSee('退勤済');
    }


    
    public function test_勤務外のとき出勤ボタンが表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('出勤');
    }

    
    public function test_出勤中のとき休憩と退勤ボタンが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 1, // 出勤中
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('休憩入');
        $response->assertSee('退勤');
    }

    
    public function test_休憩中のとき休憩戻ボタンが表示される()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 2, // 休憩中
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('休憩戻');
    }

    
    public function test_退勤済のときボタンが表示されない()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => today(),
            'status' => 3, // 退勤済
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertDontSee('出勤');
        $response->assertDontSee('休憩入');
        $response->assertDontSee('休憩戻');
        $response->assertDontSee('>退勤<');
    }
    
    public function test_退勤時刻が一覧画面に表示される()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // 出勤
        $this->post('/attendance/start');

        // 退勤
        $this->post('/attendance/end');

        // 一覧画面
        $response = $this->get('/attendance/list');

        $response->assertStatus(200);

        // DBから最新取得
        $attendance = Attendance::where('user_id', $user->id)->first();

        // 退勤時刻が表示されているか
        $response->assertSee(
            \Carbon\Carbon::parse($attendance->end_time)->format('H:i')
        );
    }
}
