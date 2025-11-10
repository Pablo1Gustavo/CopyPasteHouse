<?php declare(strict_types=1);

namespace Tests\Feature\Api\SyntaxHighlight;

use App\Models\SyntaxHighlight;
use Tests\TestCase;

class DeleteSyntaxHighlightTest extends TestCase
{
    public function test_delete_syntax_highlight_successfully(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $this->deleteJson(route('syntax-highlights.delete', $syntaxHighlight))
            ->assertOk();

        $this->assertSoftDeleted($syntaxHighlight);
    }

    public function test_delete_non_existent_syntax_highlight_fails(): void
    {
        $this->deleteJson(route('syntax-highlights.delete', fake()->uuid()))
            ->assertNotFound();
    }

    public function test_delete_already_deleted_syntax_highlight_fails(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();
        $syntaxHighlight->delete();

        $this->deleteJson(route('syntax-highlights.delete', $syntaxHighlight))
            ->assertNotFound();
    }
}