<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    private function postLogin(array $override = [])
    {
        return $this->post('/login', array_merge([
            'email' => 'test@example.com',
            'password' => 'password123',
        ], $override));
    }

    /** @test */
    public function it_shows_error_when_email_is_empty()
    {
        $response = $this->from('/login')->postLogin(['email' => '']);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('メールアドレスを入力してください', session('errors')->first('email'));
    }

    /** @test */
    public function it_shows_error_when_password_is_empty()
    {
        $response = $this->from('/login')->postLogin(['password' => '']);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードを入力してください', session('errors')->first('password'));
    }

    /** @test */
    public function it_shows_error_when_credentials_are_invalid()
    {
        // DBには存在しないユーザー情報でログイン
        $response = $this->from('/login')->postLogin([
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('ログイン情報が登録されていません', session('errors')->first('email'));
    }
}
