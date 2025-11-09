<?php declare(strict_types=1);

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson(route('auth.login'), [
            'login' => 'testuser',
            'password' => 'password123',
        ])
            ->assertCreated();

        $this->assertTrue($user->fresh()->tokens()->count() > 0);
    }

    public function test_user_can_login_with_valid_credentials_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson(route('auth.login'), [
            'login' => 'test@example.com',
            'password' => 'password123',
        ])
            ->assertCreated();
    }

    public function test_user_cannot_login_with_invalid_username(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson(route('auth.login'), [
            'login' => 'wronguser',
            'password' => 'password123',
        ])
            ->assertUnauthorized();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson(route('auth.login'), [
            'login' => 'testuser',
            'password' => 'wrongpassword',
        ])
            ->assertUnauthorized();
    }

    public function test_user_cannot_login_with_missing_fields(): void
    {
        $this->postJson(route('auth.login'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_user_cannot_login_with_empty_credentials(): void
    {
        $this->postJson(route('auth.login'), [
            'login' => '',
            'password' => '',
        ])
            ->assertUnprocessable();
    }
}
