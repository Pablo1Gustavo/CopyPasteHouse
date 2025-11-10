<?php declare(strict_types=1);

namespace Tests\Feature\Api\SyntaxHighlight;

use App\Models\SyntaxHighlight;
use Tests\TestCase;

class EditSyntaxHighlightTest extends TestCase
{
    public function test_edit_syntax_highlight_successfully(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'name' => 'Updated JavaScript',
            'extension' => 'jsx'
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight), $payload)
            ->assertOk();

        $this->assertDatabaseHas(SyntaxHighlight::class, [
            'id' => $syntaxHighlight->id,
            'name' => $payload['name'],
            'extension' => $payload['extension'],
        ]);
    }

    public function test_edit_syntax_highlight_partial_update_successfully(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create([
            'name' => 'Original Name',
            'extension' => 'orig'
        ]);

        $payload = [
            'name' => 'Updated Name Only'
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight), $payload)
            ->assertOk();

        $this->assertDatabaseHas(SyntaxHighlight::class, [
            'id'        => $syntaxHighlight->id,
            'name'      => $payload['name'],
            'extension' => 'orig',
        ]);
    }

    public function test_edit_syntax_highlight_with_duplicate_name_fails(): void
    {
        $syntaxHighlight1 = SyntaxHighlight::factory()->create();
        $syntaxHighlight2 = SyntaxHighlight::factory()->create();

        $payload = [
            'name' => $syntaxHighlight1->name
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight2), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_edit_syntax_highlight_with_duplicate_extension_fails(): void
    {
        $syntaxHighlight1 = SyntaxHighlight::factory()->create();
        $syntaxHighlight2 = SyntaxHighlight::factory()->create();

        $payload = [
            'extension' => $syntaxHighlight1->extension
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight2), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['extension']);
    }

    public function test_edit_syntax_highlight_with_same_values_successfully(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'name'      => $syntaxHighlight->name,
            'extension' => $syntaxHighlight->extension
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight), $payload)
            ->assertOk();

        $this->assertDatabaseHas(SyntaxHighlight::class, [
            'id'        => $syntaxHighlight->id,
            'name'      => $payload['name'],
            'extension' => $payload['extension'],
        ]);
    }

    public function test_edit_syntax_highlight_with_too_long_name_fails(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'name' => str_repeat('a', 300),
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_edit_syntax_highlight_with_too_long_extension_fails(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'extension' => str_repeat('a', 50),
        ];

        $this->putJson(route('syntax-highlights.edit', $syntaxHighlight), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['extension']);
    }

    public function test_edit_non_existent_syntax_highlight_fails(): void
    {
        $payload = [
            'name' => 'Updated Name'
        ];

        $this->putJson(route('syntax-highlights.edit', fake()->uuid()), $payload)
            ->assertNotFound();
    }
}