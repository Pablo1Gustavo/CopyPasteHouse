<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\{Paste, SyntaxHighlight, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class PasteCommentFactory extends Factory
{
    public function definition(): array
    {
        $content = fake()->paragraphs(
            fake()->numberBetween(1, 3),
            true
        );
        return [
            'paste_id'            => Paste::factory(),
            'user_id'             => User::factory(),
            'content'             => $content,
            'syntax_highlight_id' => fake()->boolean(70) ? SyntaxHighlight::factory() : null,
        ];
    }
}
