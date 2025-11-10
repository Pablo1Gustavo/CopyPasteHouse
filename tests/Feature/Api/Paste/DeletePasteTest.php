<?php declare(strict_types=1);

namespace Tests\Feature\Api\Paste;

use App\Models\{Paste, User};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeletePasteTest extends TestCase
{
    public function test_delete_paste_requires_authentication(): void
    {
        Auth::logout();

        $paste = Paste::factory()->create();

        $this->deleteJson(route('pastes.delete', $paste))
            ->assertUnauthorized();
    }

    public function test_delete_paste_requires_ownership(): void
    {
        $otherUser = User::factory()->create();
        $paste = Paste::factory()->for($otherUser)->create();

        $this->deleteJson(route('pastes.delete', $paste))
            ->assertForbidden();
    }

    public function test_delete_paste_successfully(): void
    {
        $paste = Paste::factory()->for($this->user)->create();

        $this->deleteJson(route('pastes.delete', $paste))
            ->assertOk();

        $this->assertSoftDeleted($paste);
    }

    public function test_delete_nonexistent_paste_with_error(): void
    {
        $this->deleteJson(route('pastes.delete', 'nonexistent-id'))
            ->assertNotFound();
    }
}
