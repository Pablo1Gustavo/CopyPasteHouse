<?php declare(strict_types=1);

namespace Tests\Feature\Api\ExpirationTime;

use App\Models\ExpirationTime;
use Tests\TestCase;

class DeleteExpirationTimeTest extends TestCase
{
    public function test_delete_expiration_time_successfully(): void
    {
        $expirationTime = ExpirationTime::factory()->create();

        $this->deleteJson(route('expiration-times.delete', $expirationTime))
            ->assertOk();

        $this->assertDatabaseMissing(ExpirationTime::class, [
            'id' => $expirationTime->id,
        ]);
    }

    public function test_delete_non_existent_expiration_time_fails(): void
    {
        $this->deleteJson(route('expiration-times.delete', 999999999))
            ->assertNotFound();
    }
}
