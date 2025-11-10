<?php declare(strict_types=1);

namespace Tests\Feature\Api\Paste;

use App\Models\{Paste, PasteLike};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ToggleLikeTest extends TestCase
{
    public function test_toggle_like_requires_authentication(): void
    {
        Auth::logout();
        $paste = Paste::factory()->create();

        $this->postJson(route('pastes.toggle-like', $paste))
            ->assertUnauthorized();
    }

    public function test_toggle_like_creates_like_when_not_liked(): void
    {
        $paste = Paste::factory()->create();

        $this->assertDatabaseMissing(PasteLike::class, [
            'paste_id' => $paste->id,
            'user_id'  => $this->user->id
        ]);

        $this->postJson(route('pastes.toggle-like', $paste))
            ->assertOk()
            ->assertJson([
                'message' => 'Paste liked!'
            ]);

        $this->assertDatabaseHas(PasteLike::class, [
            'paste_id' => $paste->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_toggle_like_removes_like_when_already_liked(): void
    {
        $paste = Paste::factory()->create();

        $paste->likes()->create([
            'user_id' => $this->user->id
        ]);

        $this->assertDatabaseHas(PasteLike::class, [
            'paste_id' => $paste->id,
            'user_id' => $this->user->id
        ]);

        $this->postJson(route('pastes.toggle-like', $paste))
            ->assertOk()
            ->assertJson([
                'message' => 'Paste unliked!'
            ]);

        $this->assertDatabaseMissing(PasteLike::class, [
            'paste_id' => $paste->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_toggle_like_on_own_paste(): void
    {
        $paste = Paste::factory()->for($this->user)->create();

        $this->postJson(route('pastes.toggle-like', $paste))
            ->assertOk()
            ->assertJson([
                'message' => 'Paste liked!'
            ]);

        $this->assertDatabaseHas(PasteLike::class, [
            'paste_id' => $paste->id,
            'user_id' => $this->user->id
        ]);
    }

    public function test_toggle_like_on_nonexistent_paste_with_error(): void
    {
        $this->postJson(route('pastes.toggle-like', fake()->uuid()))
            ->assertNotFound();
    }
}
