<?php declare(strict_types=1);

namespace Tests\Feature\Api\Paste;

use App\Models\{Paste, SyntaxHighlight};
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash};
use Tests\TestCase;

class CreatePasteTest extends TestCase
{
    public function test_create_paste_without_authentication(): void
    {
        Auth::logout();

        $payload = [
            'title' => 'Test Paste',
            'content' => 'Test content'
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(Paste::class, [
            ...$payload,
            'user_id' => null
        ]);
    }

    public function test_create_paste_successfully(): void
    {
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        $payload = [
            'syntax_highlight_id' => $syntaxHighlight->id,
            'title'               => 'Test Paste',
            'tags'                => ['php', 'laravel'],
            'content'             => 'Test content for the paste',
            'listable'            => true,
            'password'            => 'secret123',
            'expiration'          => Carbon::now()->addDays(7)->format('Y-m-d H:i'),
            'destroy_on_open'     => false
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(Paste::class, [
            'title'               => 'Test Paste',
            'content'             => 'Test content for the paste',
            'user_id'             => $this->user->id,
            'syntax_highlight_id' => $syntaxHighlight->id,
            'listable'            => true,
            'destroy_on_open'     => false
        ]);
    }

    public function test_create_paste_validates_required_fields(): void
    {
        $this->postJson(route('pastes.create'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_create_paste_validates_title_max_length(): void
    {
        $payload = [
            'title' => str_repeat('a', 51),
            'content' => 'Test content'
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_create_paste_validates_content_max_length(): void
    {
        $payload = [
            'title' => 'Test Paste',
            'content' => str_repeat('a', 512001)
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['content']);
    }

    public function test_create_paste_validates_syntax_highlight_exists(): void
    {
        $payload = [
            'title' => 'Test Paste',
            'content' => 'Test content',
            'syntax_highlight_id' => 'nonexistent-id'
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['syntax_highlight_id']);
    }

    public function test_create_paste_validates_expiration_date_format(): void
    {
        $payload = [
            'title'      => 'Test Paste',
            'content'    => 'Test content',
            'expiration' => 'invalid-date'
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['expiration']);
    }

    public function test_create_paste_validates_tags_array(): void
    {
        $payload = [
            'title'   => 'Test Paste',
            'content' => 'Test content',
            'tags'    => 'not-an-array'
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tags']);
    }

    public function test_create_paste_validates_tag_length(): void
    {
        $payload = [
            'title' => 'Test Paste',
            'content' => 'Test content',
            'tags' => [str_repeat('a', 51)]
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['tags.0']);
    }

    public function test_create_paste_hashes_password(): void
    {
        $payload = [
            'title'    => 'Test Paste',
            'content'  => 'Test content',
            'password' => 'plaintext123'
        ];

        $this->postJson(route('pastes.create'), $payload)
            ->assertCreated();

        $paste = Paste::latest()->first();
        $paste->makeVisible('password');

        $this->assertTrue(Hash::check('plaintext123', $paste->password));
    }
}
