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
            ['name' => 'None', 'extension' => 'none'],
            ['name' => 'PHP', 'extension' => 'php'],
            ['name' => 'JavaScript', 'extension' => 'javascript'],
            ['name' => 'Python', 'extension' => 'python'],
            ['name' => 'Java', 'extension' => 'java'],
            ['name' => 'C', 'extension' => 'c'],
            ['name' => 'C++', 'extension' => 'cpp'],
            ['name' => 'C#', 'extension' => 'csharp'],
            ['name' => 'Ruby', 'extension' => 'ruby'],
            ['name' => 'Go', 'extension' => 'go'],
            ['name' => 'Rust', 'extension' => 'rust'],
            ['name' => 'HTML', 'extension' => 'html'],
            ['name' => 'CSS', 'extension' => 'css'],
            ['name' => 'SQL', 'extension' => 'sql'],
            ['name' => 'Bash', 'extension' => 'bash'],
            ['name' => 'JSON', 'extension' => 'json'],
            ['name' => 'XML', 'extension' => 'xml'],
            ['name' => 'YAML', 'extension' => 'yaml'],
            ['name' => 'Markdown', 'extension' => 'markdown'],
        ];

        foreach ($syntaxHighlights as $highlight) {
            SyntaxHighlight::firstOrCreate(
                ['extension' => $highlight['extension']],
                ['name' => $highlight['name']]
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

        // Seed default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Seed test regular user
        $testUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'username' => 'testuser',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Seed default tags (created by admin, public)
        $defaultTags = [
            ['name' => 'Tutorial', 'slug' => 'tutorial', 'description' => 'Educational content and how-to guides', 'color' => '#10B981', 'is_public' => true],
            ['name' => 'Bug Fix', 'slug' => 'bug-fix', 'description' => 'Code snippets for fixing bugs', 'color' => '#EF4444', 'is_public' => true],
            ['name' => 'Algorithm', 'slug' => 'algorithm', 'description' => 'Algorithm implementations and data structures', 'color' => '#8B5CF6', 'is_public' => true],
            ['name' => 'API', 'slug' => 'api', 'description' => 'API examples and integrations', 'color' => '#F59E0B', 'is_public' => true],
            ['name' => 'Database', 'slug' => 'database', 'description' => 'Database queries and schemas', 'color' => '#3B82F6', 'is_public' => true],
            ['name' => 'Frontend', 'slug' => 'frontend', 'description' => 'HTML, CSS, and JavaScript snippets', 'color' => '#EC4899', 'is_public' => true],
            ['name' => 'Backend', 'slug' => 'backend', 'description' => 'Server-side code and logic', 'color' => '#14B8A6', 'is_public' => true],
            ['name' => 'Security', 'slug' => 'security', 'description' => 'Security-related code and best practices', 'color' => '#DC2626', 'is_public' => true],
        ];

        foreach ($defaultTags as $tagData) {
            \App\Models\Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                [
                    'user_id' => $admin->id,
                    'name' => $tagData['name'],
                    'description' => $tagData['description'],
                    'color' => $tagData['color'],
                    'is_public' => $tagData['is_public'],
                ]
            );
        }

        // Seed some user-created tags (always private for regular users)
        $userTags = [
            ['name' => 'My Notes', 'slug' => 'my-notes', 'description' => 'Personal code notes', 'color' => '#6366F1'],
            ['name' => 'Quick Reference', 'slug' => 'quick-reference', 'description' => 'Quick reference snippets', 'color' => '#A855F7'],
            ['name' => 'Work Project', 'slug' => 'work-project', 'description' => 'Code for work projects', 'color' => '#84CC16'],
        ];

        foreach ($userTags as $tagData) {
            \App\Models\Tag::firstOrCreate(
                ['slug' => $tagData['slug']],
                [
                    'user_id' => $testUser->id,
                    'name' => $tagData['name'],
                    'description' => $tagData['description'],
                    'color' => $tagData['color'],
                    'is_public' => false, // User tags are always private
                ]
            );
        }
    }
}
