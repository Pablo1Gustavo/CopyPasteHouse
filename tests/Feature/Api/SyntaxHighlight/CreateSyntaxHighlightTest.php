<?php declare(strict_types=1);

namespace Tests\Feature\Api\SyntaxHighlight;

use App\Models\SyntaxHighlight;
use Tests\TestCase;

class CreateSyntaxHighlightTest extends TestCase
{
    public function test_create_syntax_highlight_successfully(): void
    {
        $payload = [
            'name'      => 'JavaScript',
            'extension' => 'js'
        ];

        $this->postJson(route('syntax-highlights.create'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(SyntaxHighlight::class, $payload);
    }

    public function test_create_syntax_highlight_with_missing_fields_fails(): void
    {
        $this->postJson(route('syntax-highlights.create'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'extension']);
    }

    public function test_create_syntax_highlight_with_duplicate_name_fails(): void
    {
        $existingSyntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'name'      => $existingSyntaxHighlight->name,
            'extension' => 'different'
        ];

        $this->postJson(route('syntax-highlights.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_syntax_highlight_with_duplicate_extension_fails(): void
    {
        $existingSyntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'name' => 'Different Name',
            'extension' => $existingSyntaxHighlight->extension
        ];

        $this->postJson(route('syntax-highlights.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['extension']);
    }

    public function test_create_syntax_highlight_with_too_long_name_fails(): void
    {
        $payload = [
            'name' => str_repeat('a', 300),
            'extension' => 'js'
        ];

        $this->postJson(route('syntax-highlights.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_create_syntax_highlight_with_too_long_extension_fails(): void
    {
        $payload = [
            'name' => 'JavaScript',
            'extension' => str_repeat('a', 30),
        ];

        $this->postJson(route('syntax-highlights.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['extension']);
    }
}