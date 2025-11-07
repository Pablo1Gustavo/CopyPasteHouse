<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\{PasteComment, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentLikeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'comment_id' => PasteComment::factory(),
            'user_id'    => User::factory(),
            'liked_at'   => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
