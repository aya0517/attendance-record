<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Http\Middleware\VerifyCsrfToken;

class AdminLoginValidationTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
{
    parent::setUp();

    $this->withoutMiddleware([
        \App\Http\Middleware\VerifyCsrfToken::class,
    ]);
    \Auth::guard('admin')->logout();
}

    private function postAdminLogin(array $override = [])
    {
        return $this->from('/admin/login')->post('/admin/login', array_merge([
            'email' => 'admin@example.com',
            'password' => 'password123',
        ], $override));
    }

    /** @test */
    public function it_shows_error_when_admin_email_is_empty()
    {
        $this->withoutMiddleware([VerifyCsrfToken::class]);

        $response = $this->postAdminLogin(['email' => '']);
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('メールアドレスを入力してください', session('errors')->first('email'));
    }

    /** @test */
    public function it_shows_error_when_admin_password_is_empty()
    {
        $this->withoutMiddleware([VerifyCsrfToken::class]);

        $response = $this->postAdminLogin(['password' => '']);
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードを入力してください', session('errors')->first('password'));
    }

    /** @test */
    public function it_shows_error_when_admin_credentials_are_invalid()
    {
        $this->withoutMiddleware([VerifyCsrfToken::class]);

        $response = $this->postAdminLogin([
            'email' => 'wrong@example.com',
            'password' => 'wrongpass'
        ]);
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('ログイン情報が登録されていません', session('errors')->first('email'));
    }
}
