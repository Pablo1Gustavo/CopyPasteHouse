<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SyntaxHighlightFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'      => fake()->unique()->word() . '-' . fake()->randomNumber(4),
            'extension' => fake()->unique()->fileExtension(),
        ];
    }
}
