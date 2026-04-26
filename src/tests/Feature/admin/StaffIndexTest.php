<?php

namespace Tests\Feature\admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\AdminUser;
use App\Models\User;
use App\Models\Attendance;

class StaffIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_管理者はスタッフ一覧で全ユーザーの氏名とメールを確認できる()
    {
        $admin = AdminUser::factory()->create();
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.staff.index'));

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    public function test_管理者はスタッフの勤怠一覧を確認できる()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-04-10',
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get("/admin/staff/{$user->id}/attendance?month=2024-04");

        $response->assertSee('2024-04-10');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_前月ボタンで前月の勤怠が表示される()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-03-10',
        ]);

        // まず 4月画面を取得
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.staff.attendance', [
                'id' => $user->id,
                'month' => '2024-04',
            ]));

        // 前月リンクを確認
        $response->assertSee('/admin/staff/' . $user->id . '/attendance?month=2024-03');

        // 前月画面を取得
        $responsePrev = $this->actingAs($admin, 'admin')
            ->get('/admin/staff/' . $user->id . '/attendance?month=2024-03');

        // 前月の勤怠が表示されることを確認
        $responsePrev->assertSee('2024-03-10');
    }


    public function test_翌月ボタンで翌月の勤怠が表示される()
    {
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-05-10',
        ]);

        // まず 4月画面を取得
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.staff.attendance', [
                'id' => $user->id,
                'month' => '2024-04',
            ]));

        // 翌月リンクを確認
        $response->assertSee('/admin/staff/' . $user->id . '/attendance?month=2024-05');

        // 翌月画面を取得
        $responseNext = $this->actingAs($admin, 'admin')
            ->get('/admin/staff/' . $user->id . '/attendance?month=2024-05');

        // 翌月の勤怠が表示されることを確認
        $responseNext->assertSee('2024-05-10');
    }


    public function test_詳細ボタンで勤怠詳細画面に遷移する()
    {
        $this->withoutExceptionHandling();
        $admin = AdminUser::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2024-04-10 09:00:00', // ← ここ重要
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get("/admin/staff/{$user->id}/attendance?month=2024-04");

        $response->assertStatus(200);

        // 詳細リンクがあるか
        $response->assertSee(
            route('admin.attendance.detail', $attendance->id),
            false
        );
    }
}
