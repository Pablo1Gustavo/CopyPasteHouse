<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\UserSettings;
use App\Models\User;

class UserSettingsService
{
    /**
     * Get user settings by user ID
     */
    public function show(string $userId): ?UserSettings
    {
        return UserSettings::with('user')->find($userId);
    }

    /**
     * Get all user settings (for admin)
     */
    public function list()
    {
        return UserSettings::with('user')->get();
    }

    /**
     * Create or update user settings
     * 
     * @param array{
     *     timezone?: string,
     *     language?: string,
     *     theme?: string
     * } $data
     */
    public function createOrUpdate(string $userId, array $data): UserSettings
    {
        $data = array_filter($data, fn ($value) => $value !== null);
        
        return UserSettings::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }

    /**
     * Delete user settings
     */
    public function delete(string $userId): bool
    {
        $settings = UserSettings::find($userId);
        
        if (!$settings) {
            return false;
        }

        return $settings->delete();
    }

    /**
     * Get settings by timezone
     */
    public function findByTimezone(string $timezone)
    {
        return UserSettings::with('user')
            ->where('timezone', $timezone)
            ->get();
    }

    /**
     * Get settings by language
     */
    public function findByLanguage(string $language)
    {
        return UserSettings::with('user')
            ->where('language', $language)
            ->get();
    }

    /**
     * Get settings by theme
     */
    public function findByTheme(string $theme)
    {
        return UserSettings::with('user')
            ->where('theme', $theme)
            ->get();
    }

    /**
     * Get users with specific settings combination
     */
    public function findBySettings(array $criteria)
    {
        $query = UserSettings::with('user');

        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }

        return $query->get();
    }
}
