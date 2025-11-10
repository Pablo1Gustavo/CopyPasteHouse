<?php declare(strict_types=1);

namespace Tests\Feature\Api\PasteComment;

use App\Models\{Paste, PasteComment, SyntaxHighlight};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreatePasteCommentTest extends TestCase
{
    public function test_create_paste_comment_successfully(): void
    {
        $paste = Paste::factory()->create();
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'content' => 'This is a test comment',
            'syntax_highlight_id' => $syntaxHighlight->id
        ];

        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertCreated()
            ->assertJson([
                'message' => 'Comment created successfully.'
            ]);

        $this->assertDatabaseHas(PasteComment::class, [
            'paste_id' => $paste->id,
            'user_id' => $this->user->id,
            'content' => $payload['content'],
            'syntax_highlight_id' => $syntaxHighlight->id,
        ]);
    }

    public function test_create_paste_comment_without_syntax_highlight_successfully(): void
    {
        $paste = Paste::factory()->create();

        $payload = [
            'content' => 'Comment without syntax highlighting',
        ];

        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertCreated()
            ->assertJson([
                'message' => 'Comment created successfully.'
            ]);

        $this->assertDatabaseHas(PasteComment::class, [
            'paste_id' => $paste->id,
            'user_id' => $this->user->id,
            'content' => $payload['content'],
            'syntax_highlight_id' => null,
        ]);
    }

    public function test_create_paste_comment_with_missing_content_fails(): void
    {
        $paste = Paste::factory()->create();

        $this->postJson(route('pastes.comments.create', $paste), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_create_paste_comment_with_empty_content_fails(): void
    {
        $paste = Paste::factory()->create();

        $payload = [
            'content' => '',
        ];

        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_create_paste_comment_with_too_long_content_fails(): void
    {
        $paste = Paste::factory()->create();

        $payload = [
            'content' => str_repeat('a', 10001), // Over the 10000 char limit
        ];

        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_create_paste_comment_with_invalid_syntax_highlight_id_fails(): void
    {
        $paste = Paste::factory()->create();

        $payload = [
            'content' => 'Valid content',
            'syntax_highlight_id' => 'invalid-uuid'
        ];

        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['syntax_highlight_id']);
    }

    public function test_create_paste_comment_with_non_existent_syntax_highlight_id_fails(): void
    {
        $paste = Paste::factory()->create();

        $payload = [
            'content' => 'Valid content',
            'syntax_highlight_id' => fake()->uuid()
        ];

        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['syntax_highlight_id']);
    }

    public function test_create_comment_for_non_existent_paste_fails(): void
    {
        $payload = [
            'content' => 'Valid content',
        ];

        $this->postJson(route('pastes.comments.create', fake()->uuid()), $payload)
            ->assertNotFound();
    }

    public function test_create_paste_comment_without_authentication_fails(): void
    {
        Auth::logout();
        $paste = Paste::factory()->create();

        $payload = [
            'content' => 'Valid content',
        ];
        $this->postJson(route('pastes.comments.create', $paste), $payload)
            ->assertUnauthorized();
    }
}
