<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\{Paste, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class PasteLikeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paste_id' => Paste::factory(),
            'user_id'  => User::factory(),
            'liked_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
