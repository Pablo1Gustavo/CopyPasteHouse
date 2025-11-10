<?php declare(strict_types=1);

namespace Tests\Feature\Api\Paste;

use App\Models\{Paste, User};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MyPastesTest extends TestCase
{
    public function test_my_pastes_requires_authentication(): void
    {
        Auth::logout();

        $this->getJson(route('pastes.my-pastes'))
            ->assertUnauthorized();
    }

    public function test_my_pastes_returns_only_authenticated_users_pastes(): void
    {
        $otherUser = User::factory()->create();

        Paste::factory()->for($this->user)->count(3)->create();
        Paste::factory()->for($otherUser)->count(2)->create();

        $response = $this->getJson(route('pastes.my-pastes'))
            ->assertOk()
            ->assertJsonCount(3);

        $pasteUserIds = collect($response->json())->pluck('user_id')->unique();

        $this->assertCount(1, $pasteUserIds);
        $this->assertEquals($this->user->id, $pasteUserIds->first());
    }

    public function test_my_pastes_respects_filters(): void
    {
        Paste::factory()->for($this->user)->create(['title' => 'JavaScript Code']);
        Paste::factory()->for($this->user)->create(['title' => 'PHP Script']);
        Paste::factory()->for($this->user)->create(['title' => 'Another JavaScript']);

        $this->getJson(route('pastes.my-pastes', ['title' => 'JavaScript']))
            ->assertOk()
            ->assertJsonCount(2);
    }
}
