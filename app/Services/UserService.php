<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * @param array{
     *     username: string,
     *     email: string,
     *     password: string
     * } $data
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function show(string $id): ?User
    {
        return User::with(['settings', 'pastes', 'comments'])
                   ->find($id);
    }

    /**
     * @param array{
     *     username?: string,
     *     email?: string
     * } $data
     */
    public function edit(User $user, array $data): User
    {
        $data = array_filter($data, fn ($value) => $value !== null);
        $user->update($data);
        return $user;
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password))
        {
            return false;
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return true;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
