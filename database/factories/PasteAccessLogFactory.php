<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\{Paste, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class PasteAccessLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paste_id'    => Paste::factory(),
            'user_id'     => User::factory(),
            'ip'          => fake()->ipv6(),
            'user_agent'  => fake()->userAgent(),
            'access_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'access_date' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }
}
