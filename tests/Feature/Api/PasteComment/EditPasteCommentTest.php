<?php declare(strict_types=1);

namespace Tests\Feature\Api\PasteComment;

use App\Exceptions\NotOwner;
use App\Models\{PasteComment, SyntaxHighlight, User};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class EditPasteCommentTest extends TestCase
{
    public function test_edit_paste_comment_successfully(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'content' => 'Updated comment content',
            'syntax_highlight_id' => $syntaxHighlight->id
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertOk();

        $this->assertDatabaseHas(PasteComment::class, [
            'id'                  => $comment->id,
            'content'             => $payload['content'],
            'syntax_highlight_id' => $syntaxHighlight->id,
        ]);
    }

    public function test_edit_paste_comment_with_missing_content_fails(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $this->putJson(route('pastes.comments.edit', $comment), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_edit_paste_comment_with_empty_content_fails(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $payload = [
            'content' => '',
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_edit_paste_comment_with_too_long_content_fails(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $payload = [
            'content' => str_repeat('a', 10001),
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_edit_paste_comment_with_invalid_syntax_highlight_id_fails(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $payload = [
            'content' => 'Valid content',
            'syntax_highlight_id' => 'invalid-uuid'
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['syntax_highlight_id']);
    }

    public function test_edit_paste_comment_with_non_existent_syntax_highlight_id_fails(): void
    {
        $comment = PasteComment::factory()->create([
            'user_id' => $this->user->id
        ]);

        $payload = [
            'content' => 'Valid content',
            'syntax_highlight_id' => fake()->uuid()
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['syntax_highlight_id']);
    }

    public function test_edit_non_owned_paste_comment_fails(): void
    {
        $otherUser = User::factory()->create();
        $comment = PasteComment::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $payload = [
            'content' => 'Attempted update',
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertForbidden();
    }

    public function test_edit_non_existent_paste_comment_fails(): void
    {
        $payload = [
            'content' => 'Valid content',
        ];

        $this->putJson(route('pastes.comments.edit', fake()->uuid()), $payload)
            ->assertNotFound();
    }

    public function test_edit_paste_comment_without_authentication_fails(): void
    {
        Auth::logout();
        $comment = PasteComment::factory()->create();

        $payload = [
            'content' => 'Valid content',
        ];

        $this->putJson(route('pastes.comments.edit', $comment), $payload)
            ->assertUnauthorized();
    }
}
