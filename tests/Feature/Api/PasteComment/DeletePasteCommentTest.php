<?php declare(strict_types=1);

namespace Tests\Feature\Api\PasteComment;

use App\Exceptions\NotOwner;
use App\Models\{PasteComment, User};
use Tests\TestCase;

class DeletePasteCommentTest extends TestCase
{
    public function test_delete_paste_comment_successfully(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->deleteJson(route('pastes.comments.delete', $comment))
            ->assertOk();

        $this->assertSoftDeleted($comment);
    }

    public function test_delete_non_owned_paste_comment_fails(): void
    {
        $otherUser = User::factory()->create();
        $comment = PasteComment::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $this->deleteJson(route('pastes.comments.delete', $comment))
            ->assertForbidden();
    }

    public function test_delete_non_existent_paste_comment_fails(): void
    {
        $this->deleteJson(route('pastes.comments.delete', fake()->uuid()))
            ->assertNotFound();
    }

    public function test_delete_paste_comment_without_authentication_fails(): void
    {
        $this->app['auth']->forgetGuards();
        $comment = PasteComment::factory()->create();

        $this->deleteJson(route('pastes.comments.delete', $comment))
            ->assertUnauthorized();
    }
}
