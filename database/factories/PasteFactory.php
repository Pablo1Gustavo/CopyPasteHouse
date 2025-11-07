<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\{SyntaxHighlight, User};
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PasteFactory extends Factory
{
    public function definition(): array
    {
        $content = fake()->paragraphs(
            fake()->numberBetween(1, 10),
            true
        );

        $password = fake()->optional(0.1)->boolean()
            ? Hash::make('password')
            : null;

        $tags = fake()->optional(0.3)->passthrough(
            array_map(
                fn () => fake()->unique()->word(),
                range(1, fake()->numberBetween(1, 5))
            )
        );

        $expiration = fake()->optional(0.2)->dateTimeBetween('now', '+1 year');

        return [
            'user_id'             => User::factory(),
            'syntax_highlight_id' => SyntaxHighlight::factory(),
            'title'               => fake()->sentence(fake()->numberBetween(2, 8)),
            'content'             => $content,
            'tags'                => $tags,
            'listable'            => fake()->boolean(90),
            'password'            => $password,
            'destroy_on_open'     => fake()->boolean(5),
            'expiration'          => $expiration,
        ];
    }

    public function anonymous(): static
    {
        return $this->state(fn (array $attributes) =>
        [
            'user_id' => null,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) =>
        [
            'listable' => false,
        ]);
    }
}
