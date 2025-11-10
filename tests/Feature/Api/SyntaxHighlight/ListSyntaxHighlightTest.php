<?php declare(strict_types=1);

namespace Tests\Feature\Api\SyntaxHighlight;

use App\Models\SyntaxHighlight;
use Tests\TestCase;

class ListSyntaxHighlightTest extends TestCase
{
    public function test_list_syntax_highlights_successfully(): void
    {
        $syntaxHighlights = SyntaxHighlight::factory(3)->create();

        $response = $this->getJson(route('syntax-highlights.list'))
            ->assertOk();

        foreach ($syntaxHighlights as $syntaxHighlight) {
            $response->assertJsonFragment($syntaxHighlight->toArray());
        }
    }
}