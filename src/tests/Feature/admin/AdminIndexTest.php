<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\AdminUser;
use App\Models\User;
use App\Models\Attendance;

class AdminIndexTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_全ユーザーの勤怠が表示される()
    {
        $admin = AdminUser::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user1->id,
            'date' => now(),
            'start_time' => '09:00',
            'end_time' => '18:00',
        ]);

        Attendance::factory()->create([
            'user_id' => $user2->id,
            'date' => now(),
            'start_time' => '10:00',
            'end_time' => '19:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        // 両方表示される
        $response->assertSee('09:00');
        $response->assertSee('10:00');
    }

    public function test_現在日付が表示される()
    {
        $admin = AdminUser::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);

        $today = now()->format('Y年n月j日');

        $response->assertSee($today);
    }

    public function test_前日が表示される()
    {
        $admin = AdminUser::factory()->create();

        $yesterday = now()->subDay();

        // テスト用の昨日のユーザーデータを作る
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $yesterday,
            'start_time' => '08:00',
            'end_time' => '17:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard', [
                'date' => $yesterday->format('Y-m-d')
            ]));

        $response->assertStatus(200);

        $response->assertSee('08:00');
    }

    public function test_翌日が表示される()
    {
        $admin = AdminUser::factory()->create();

        $tomorrow = now()->addDay();

        // テスト用の明日のユーザーデータを作る
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $tomorrow,
            'start_time' => '11:00',
            'end_time' => '20:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.dashboard', [
                'date' => $tomorrow->format('Y-m-d')
            ]));

        $response->assertStatus(200);

        $response->assertSee('11:00');
    }
}
