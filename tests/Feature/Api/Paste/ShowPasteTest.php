<?php declare(strict_types=1);

namespace Tests\Feature\Api\Paste;

use App\Models\{Paste, PasteAccessLog};
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShowPasteTest extends TestCase
{
    public function test_show_paste_without_password_succeeds(): void
    {
        $paste = Paste::factory()->create(['password' => null]);

        $this->getJson(route('pastes.show', $paste))
            ->assertOk()
            ->assertJsonMissing(['password']);
    }

    public function test_show_paste_creates_access_log(): void
    {
        $paste = Paste::factory()->create(['password' => null]);

        $this->assertDatabaseCount(PasteAccessLog::class, 0);

        $this->getJson(route('pastes.show', $paste))
            ->assertOk();

        $this->assertDatabaseCount(PasteAccessLog::class, 1);

        $accessLog = PasteAccessLog::first();
        $this->assertEquals($paste->id, $accessLog->paste_id);
    }

    public function test_show_paste_with_authenticated_user_records_user_in_log(): void
    {
        $paste = Paste::factory()->create(['password' => null]);

        $this->getJson(route('pastes.show', $paste))
            ->assertOk();

        $accessLog = PasteAccessLog::latest()->first();
        $this->assertEquals($this->user->id, $accessLog->user_id);
    }

    public function test_show_password_protected_paste_without_password_returns_unauthorized(): void
    {
        $paste = Paste::factory()->create(['password' => Hash::make('secret123')]);

        $this->getJson(route('pastes.show', $paste))
            ->assertUnauthorized();
    }

    public function test_show_password_protected_paste_with_correct_password_succeeds(): void
    {
        $paste = Paste::factory()->create(['password' => Hash::make('secret123')]);

        $this->getJson(route('pastes.show', $paste) . '?password=secret123')
            ->assertOk();
    }

    public function test_show_password_protected_paste_with_wrong_password_fails(): void
    {
        $paste = Paste::factory()->create(['password' => Hash::make('secret123')]);

        $this->getJson(route('pastes.show', $paste) . '?password=wrongpassword')
            ->assertUnauthorized();
    }

    public function test_show_paste_with_destroy_on_open_deletes_paste(): void
    {
        $paste = Paste::factory()->create([
            'password' => null,
            'destroy_on_open' => true
        ]);

        $this->getJson(route('pastes.show', $paste))
            ->assertOk();

        $this->assertSoftDeleted($paste);
    }

    public function test_show_nonexistent_paste_returns_with_error(): void
    {
        $this->getJson(route('pastes.show', fake()->uuid()))
            ->assertNotFound();
    }
}
