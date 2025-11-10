<?php declare(strict_types=1);

namespace Tests\Feature\Api\PasteComment;

use App\Models\{Paste, PasteComment};
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ListPasteCommentsTest extends TestCase
{
    public function test_list_paste_comments_successfully(): void
    {
        $paste = Paste::factory()->create();
        $comments = PasteComment::factory(3)->for($paste)->create();

        $response = $this->getJson(route('pastes.comments.list', $paste))
            ->assertOk();

        foreach ($comments as $comment)
        {
            $response->assertJsonFragment([
                'id' => $comment->id,
                'content' => $comment->content,
            ]);
        }
    }

    public function test_list_comments_for_non_existent_paste_fails(): void
    {
        $this->getJson(route('pastes.comments.list', fake()->uuid()))
            ->assertNotFound();
    }

    public function test_list_paste_comments_without_authentication_fails(): void
    {
        Auth::logout();
        $paste = Paste::factory()->create();

        $this->getJson(route('pastes.comments.list', $paste))
            ->assertUnauthorized();
    }
}
