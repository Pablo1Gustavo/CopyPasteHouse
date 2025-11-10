<?php declare(strict_types=1);

namespace Tests\Feature\Api\PasteComment;

use App\Models\{CommentLike, PasteComment};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ToggleLikePasteCommentTest extends TestCase
{
    public function test_like_paste_comment_successfully(): void
    {
        $comment = PasteComment::factory()->create();

        $this->patchJson(route('pastes.comments.like', $comment))
            ->assertOk()
            ->assertJson([
                'message' => 'Comment liked.'
            ]);

        $this->assertDatabaseHas(CommentLike::class, [
            'comment_id' => $comment->id,
            'user_id'    => $this->user->id,
        ]);
    }

    public function test_unlike_paste_comment_successfully(): void
    {
        $comment = PasteComment::factory()->create();

        $comment->likes()->create([
            'user_id' => $this->user->id,
        ]);

        $this->patchJson(route('pastes.comments.like', $comment))
            ->assertOk()
            ->assertJson([
                'message' => 'Comment unliked.'
            ]);

        $this->assertDatabaseMissing(CommentLike::class, [
            'comment_id' => $comment->id,
            'user_id'    => $this->user->id,
        ]);
    }


    public function test_toggle_like_non_existent_paste_comment_fails(): void
    {
        $this->patchJson(route('pastes.comments.like', fake()->uuid()))
            ->assertNotFound();
    }

    public function test_toggle_like_paste_comment_without_authentication_fails(): void
    {
        Auth::logout();
        $comment = PasteComment::factory()->create();

        $this->patchJson(route('pastes.comments.like', $comment))
            ->assertUnauthorized();
    }
}
