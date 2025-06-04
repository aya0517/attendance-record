<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    private function postRegister(array $override = [])
    {
        return $this->from('/register')->post('/register', array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $override));
    }

    /** @test */
    public function it_shows_error_when_name_is_empty()
    {
        $response = $this->postRegister(['name' => '']);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);
        $this->assertStringContainsString('お名前を入力してください', session('errors')->first('name'));
    }

    /** @test */
    public function it_shows_error_when_email_is_empty()
    {
        $response = $this->postRegister(['email' => '']);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
        $this->assertStringContainsString('メールアドレスを入力してください', session('errors')->first('email'));
    }

    /** @test */
    public function it_shows_error_when_password_is_less_than_8_characters()
    {
        $response = $this->postRegister([
            'password' => 'short',
            'password_confirmation' => 'short'
        ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードは8文字以上で入力してください', session('errors')->first('password'));
    }

    /** @test */
    public function it_shows_error_when_passwords_do_not_match()
    {
        $response = $this->postRegister([
            'password' => 'password123',
            'password_confirmation' => 'different'
        ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードと一致しません', session('errors')->first('password'));
    }

    /** @test */
    public function it_shows_error_when_password_is_empty()
    {
        $response = $this->postRegister([
            'password' => '',
            'password_confirmation' => ''
        ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
        $this->assertStringContainsString('パスワードを入力してください', session('errors')->first('password'));
    }

    /** @test */
    public function it_registers_user_successfully_with_valid_data()
    {
        $response = $this->postRegister();
        $response->assertRedirect('/attendance');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }
}
