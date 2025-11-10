<?php declare(strict_types=1);

namespace Tests\Feature\Api\Auth;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_authenticated_user_can_logout(): void
    {
        $this->postJson(route('auth.logout'))
            ->assertOk();

        $this->assertTrue($this->user->fresh()->tokens()->count() === 0);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        Auth::logout();

        $this->postJson(route('auth.logout'))
            ->assertUnauthorized();
    }
}
