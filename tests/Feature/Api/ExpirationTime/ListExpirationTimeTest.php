<?php declare(strict_types=1);

namespace Tests\Feature\Api\ExpirationTime;

use App\Models\ExpirationTime;
use Tests\TestCase;

class ListExpirationTimeTest extends TestCase
{
    public function test_list_expiration_times_successfully(): void
    {
        $expirationTimes = ExpirationTime::factory(3)->create();

        $response = $this->getJson(route('expiration-times.list'))
            ->assertOk();

        foreach ($expirationTimes as $expirationTime)
        {
            $response->assertJsonFragment($expirationTime->toArray());
        }
    }
}
