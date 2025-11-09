<?php declare(strict_types=1);

namespace Tests\Feature\Api\ExpirationTime;

use App\Models\ExpirationTime;
use Tests\TestCase;

class CreateExpirationTimeTest extends TestCase
{
    public function test_create_expiration_time_successfully(): void
    {
        $payload = [
            'minutes' => 120,
            'label' => '2 hours'
        ];

        $this->postJson(route('expiration-times.create'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(ExpirationTime::class, $payload);
    }

    public function test_create_expiration_time_with_missing_fields_fails(): void
    {
        $this->postJson(route('expiration-times.create'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minutes', 'label']);
    }

    public function test_create_expiration_time_with_negative_minutes_fails(): void
    {
        $payload = [
            'minutes' => -1,
            'label' => 'Negative minutes'
        ];

        $this->postJson(route('expiration-times.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minutes']);
    }

    public function test_create_expiration_time_with_zero_minutes_fails(): void
    {
        $payload = [
            'minutes' => 0,
            'label' => 'Zero minutes'
        ];

        $this->postJson(route('expiration-times.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minutes']);
    }

    public function test_create_expiration_time_with_too_long_minutes_fails(): void
    {
        $payload = [
            'minutes' => 10000000,
            'label' => 'Too long minutes'
        ];

        $this->postJson(route('expiration-times.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['minutes']);
    }

    public function test_create_expiration_time_with_too_long_lebel_fails(): void
    {
        $payload = [
            'minutes' => 123,
            'label' => fake()->text(100),
        ];

        $this->postJson(route('expiration-times.create'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['label']);
    }
}
