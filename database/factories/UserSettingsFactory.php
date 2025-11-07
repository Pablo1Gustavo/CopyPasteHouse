<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserSettingsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'  => User::factory(),
            'timezone' => fake()->timezone(),
            'language' => fake()->languageCode(),
            'theme'    => fake()->randomElement(['light', 'dark', 'system']),
        ];
    }
}
