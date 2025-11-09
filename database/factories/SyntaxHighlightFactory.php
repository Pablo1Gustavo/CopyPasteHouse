<?php declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SyntaxHighlightFactory extends Factory
{
    public function definition(): array
    {
        $language = substr(fake()->unique()->name(), 25);

        return [
            'name' => $language,
            'extension' => str($language)->substr(0,5)->slug()->replace('-', '')->toString(),
        ];
    }
}
