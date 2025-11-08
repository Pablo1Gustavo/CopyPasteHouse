<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
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

    /**
     * @return Collection<User>
     */
    public function list(array $filters = []): Collection
    {
        $query = User::query()->with(['settings']);

        if (isset($filters['username']))
        {
            $query->where('username', 'LIKE', '%' . $filters['username'] . '%');
        }

        if (isset($filters['email']))
        {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        if (isset($filters['created_after']))
        {
            $query->where('created_at', '>=', $filters['created_after']);
        }

        if (isset($filters['created_before']))
        {
            $query->where('created_at', '<=', $filters['created_before']);
        }

        if (isset($filters['limit']))
        {
            $query->limit($filters['limit']);
        }

        if (isset($filters['offset']))
        {
            $query->offset($filters['offset']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->get();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    /**
     * @return Collection<User>
     */
    public function getUsersWithMostPastes(int $limit = 10): Collection
    {
        return User::withCount('pastes')
                   ->orderBy('pastes_count', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * @return Collection<User>
     */
    public function getUsersWithMostComments(int $limit = 10): Collection
    {
        return User::withCount('comments')
                   ->orderBy('comments_count', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * @return Collection<User>
     */
    public function getUsersWithMostLikes(int $limit = 10): Collection
    {
        return User::withCount(['likes', 'commentLikes'])
                   ->orderByRaw('(likes_count + comment_likes_count) desc')
                   ->limit($limit)
                   ->get();
    }

    public function getUserStats(User $user): array
    {
        $user->loadCount(['pastes', 'comments', 'likes', 'commentLikes']);

        return [
            'pastes_count' => $user->pastes_count,
            'comments_count' => $user->comments_count,
            'paste_likes_count' => $user->likes_count,
            'comment_likes_count' => $user->comment_likes_count,
            'total_likes_count' => $user->likes_count + $user->comment_likes_count,
        ];
    }

    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
