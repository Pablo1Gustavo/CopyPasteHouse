<?php declare(strict_types=1);

namespace Tests\Feature\Api\Paste;

use App\Models\{Paste, SyntaxHighlight, User};
use Carbon\Carbon;
use Tests\TestCase;

class ListPastesTest extends TestCase
{
    public function test_list_pastes_returns_all_pastes_without_filters(): void
    {
        Paste::factory()->count(3)->create();

        $this->getJson(route('pastes.list'))
            ->assertOk()
            ->assertJsonCount(3);
    }

    public function test_list_pastes_filters_by_user_id(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Paste::factory()->for($user1)->count(2)->create();
        Paste::factory()->for($user2)->count(1)->create();

        $this->getJson(route('pastes.list', ['user_id' => $user1->id]))
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_list_pastes_filters_by_syntax_highlight_id(): void
    {
        $syntaxHighlight1 = SyntaxHighlight::factory()->create();
        $syntaxHighlight2 = SyntaxHighlight::factory()->create();

        Paste::factory()->for($syntaxHighlight1)->count(2)->create();
        Paste::factory()->for($syntaxHighlight2)->count(1)->create();

        $this->getJson(route('pastes.list', ['syntax_highlight_id' => $syntaxHighlight1->id]))
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_list_pastes_filters_by_title(): void
    {
        Paste::factory()->create(['title' => 'JavaScript Code']);
        Paste::factory()->create(['title' => 'PHP Script']);
        Paste::factory()->create(['title' => 'Another JavaScript Example']);

        $this->getJson(route('pastes.list', ['title' => 'JavaScript']))
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_list_pastes_filters_by_tags(): void
    {
        Paste::factory()->create(['tags' => ['php', 'laravel']]);
        Paste::factory()->create(['tags' => ['javascript', 'react']]);
        Paste::factory()->create(['tags' => ['php', 'symfony']]);

        $this->getJson(route('pastes.list', ['tags' => ['php']]))
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_list_pastes_filters_by_created_after(): void
    {
        $oldPaste = Paste::factory()->create(['created_at' => Carbon::now()->subDays(10)]);
        $newPaste = Paste::factory()->create(['created_at' => Carbon::now()->subDays(2)]);

        $this->getJson(route('pastes.list', [
            'created_after' => Carbon::now()->subDays(5)->format('Y-m-d H:i')
        ]))
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_list_pastes_filters_by_created_before(): void
    {
        $oldPaste = Paste::factory()->create(['created_at' => Carbon::now()->subDays(10)]);
        $newPaste = Paste::factory()->create(['created_at' => Carbon::now()->subDays(2)]);

        $this->getJson(route('pastes.list', [
            'created_before' => Carbon::now()->subDays(5)->format('Y-m-d H:i')
        ]))
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_list_pastes_with_invalid_user_id_returns_validation_error(): void
    {
        $this->getJson(route('pastes.list', ['user_id' => fake()->uuid()]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['user_id']);
    }

    public function test_list_pastes_with_invalid_syntax_highlight_id_returns_validation_error(): void
    {
        $this->getJson(route('pastes.list', ['syntax_highlight_id' => fake()->uuid()]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['syntax_highlight_id']);
    }

    public function test_list_pastes_with_invalid_date_returns_validation_error(): void
    {
        $this->getJson(route('pastes.list', ['created_after' => 'invalid-date']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['created_after']);
    }

    public function test_list_pastes_combines_multiple_filters(): void
    {
        $user = User::factory()->create();
        $syntaxHighlight = SyntaxHighlight::factory()->create();

        Paste::factory()
            ->for($user)
            ->for($syntaxHighlight)
            ->create(['title' => 'Test Code', 'created_at' => Carbon::now()->subDays(1)]);

        Paste::factory()->for($user)->create(['title' => 'Other Code']);
        Paste::factory()->create(['title' => 'Test Code']);

        $response = $this->getJson(route('pastes.list', [
            'user_id'             => $user->id,
            'syntax_highlight_id' => $syntaxHighlight->id,
            'title'               => 'Test',
            'created_after'       => Carbon::now()->subDays(2)->format('Y-m-d H:i')
        ]));

        $response
            ->assertOk()
            ->assertJsonCount(1);
    }
}
