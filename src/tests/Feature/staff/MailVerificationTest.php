<?php

namespace Tests\Feature\staff;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class MailVerificationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_会員登録後に認証メールが送信される()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // ユーザー取得
        $user = User::where('email', 'test@example.com')->first();

        // ユーザーが作成されているか
        $this->assertNotNull($user);

        // 認証メールが送信されているか確認
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_認証はこちらからボタンでMailHogに遷移するリンクがある()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/mailenable');

        $response->assertStatus(200);

        // テキスト確認
        $response->assertSee('認証はこちらから');

        // リンク確認
        $response->assertSee('http://localhost:8025');
    }

    public function test_メール認証完了後に勤怠画面に遷移する()
    {
        $user = User::factory()->unverified()->create();

        // 認証リンク（メールの代わり）
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($url);

        // 勤怠画面にリダイレクト
        $response->assertRedirect('/attendance');

        // 認証済みになっているか
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
