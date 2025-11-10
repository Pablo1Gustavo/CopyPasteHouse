<?php

namespace Database\Seeders;

use App\Models\ExpirationTime;
use App\Models\SyntaxHighlight;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Syntax Highlights
        $syntaxHighlights = [
            ['label' => 'None', 'value' => 'none'],
            ['label' => 'PHP', 'value' => 'php'],
            ['label' => 'JavaScript', 'value' => 'javascript'],
            ['label' => 'Python', 'value' => 'python'],
            ['label' => 'Java', 'value' => 'java'],
            ['label' => 'C', 'value' => 'c'],
            ['label' => 'C++', 'value' => 'cpp'],
            ['label' => 'C#', 'value' => 'csharp'],
            ['label' => 'Ruby', 'value' => 'ruby'],
            ['label' => 'Go', 'value' => 'go'],
            ['label' => 'Rust', 'value' => 'rust'],
            ['label' => 'HTML', 'value' => 'html'],
            ['label' => 'CSS', 'value' => 'css'],
            ['label' => 'SQL', 'value' => 'sql'],
            ['label' => 'Bash', 'value' => 'bash'],
            ['label' => 'JSON', 'value' => 'json'],
            ['label' => 'XML', 'value' => 'xml'],
            ['label' => 'YAML', 'value' => 'yaml'],
            ['label' => 'Markdown', 'value' => 'markdown'],
        ];

        foreach ($syntaxHighlights as $highlight) {
            SyntaxHighlight::firstOrCreate(
                ['value' => $highlight['value']],
                ['label' => $highlight['label']]
            );
        }

        // Seed Expiration Times (minutes as primary key, label as display)
        $expirationTimes = [
            ['minutes' => 10, 'label' => '10 Minutes'],
            ['minutes' => 60, 'label' => '1 Hour'],
            ['minutes' => 1440, 'label' => '1 Day'],
            ['minutes' => 10080, 'label' => '1 Week'],
            ['minutes' => 20160, 'label' => '2 Weeks'],
            ['minutes' => 43200, 'label' => '1 Month'],
            ['minutes' => 259200, 'label' => '6 Months'],
            ['minutes' => 525600, 'label' => '1 Year'],
        ];

        foreach ($expirationTimes as $time) {
            ExpirationTime::firstOrCreate(
                ['minutes' => $time['minutes']],
                ['label' => $time['label']]
            );
        }

        // Seed test user (optional)
        // User::factory()->create([
        //     'username' => 'testuser',
        //     'email' => 'test@example.com',
        // ]);
    }
}
