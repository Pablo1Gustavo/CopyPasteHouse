<?php declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\{RefreshDatabase, TestCase as BaseTestCase};

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }
}
