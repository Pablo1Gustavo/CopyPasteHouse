<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExpirationTimeFactory extends Factory
{
    public function definition(): array
    {
        $minutes = fake()->unique()->numberBetween(0, 100000);

        return [
            'minutes' => $minutes,
            'label'   => "{$minutes} minutes",
        ];
    }
}
