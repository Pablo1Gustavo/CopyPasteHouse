<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\{Paste, User};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{DB, Hash};

class PasteService
{
    /**
     * @param array{
     *     user_id?: string,
     *     syntax_highlight_id?: string,
     *     title?: string,
     *     tags?: array<string>,
     *     created_after?: Carbon,
     *     created_before?: Carbon,
     * } $filters
     * @return Collection<Paste>
     */
    public function list(array $filters = []): Collection
    {
        $query = Paste::query()
            ->when(isset($filters['user_id']), fn (Builder $q) =>
                $q->where('user_id', $filters['user_id'])
            )
            ->when(isset($filters['syntax_highlight_id']), fn (Builder $q) =>
                $q->where('syntax_highlight_id', $filters['syntax_highlight_id'])
            )
            ->when(isset($filters['title']), fn (Builder $q) =>
                $q->whereLike('title', '%' . $filters['title'] . '%')
            )
            ->when(isset($filters['created_after']), fn (Builder $q) =>
                $q->where('created_at', '>=', $filters['created_after'])
            )
            ->when(isset($filters['created_before']), fn (Builder $q) =>
                $q->where('created_at', '<=', $filters['created_before'])
            );

        if (isset($filters['tags']))
        {
            sort($filters['tags']);
            $pattern = '%,' . implode(',%,', $filters['tags']) . ',%';
            $query->where('tags', 'LIKE', $pattern);
        }

        return $query->get();
    }

    public function access(
        ?User $user,
        Paste $paste,
        ?string $password,
        string $ipAddress,
        string $userAgent
    ): Paste {
        $paste->makeVisible('password');

        $senhaValida = isset($paste->password) && Hash::check($password, $paste->password);
        if (!$senhaValida)
        {
            throw new \Exception('Invalid password for paste access.');
        }

        DB::transaction(function () use ($user, $paste, $ipAddress, $userAgent)
        {
            $paste->accessLogs()->create([
                'user_id'    => $user?->id,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
            if ($paste->destroy_on_open)
            {
                $paste->delete();
            }
        });

        $paste->makeHidden('password');
        return $paste;
    }

    /**
     * @param array{
     *     syntax_highlight_id?: string,
     *     title: string,
     *     tags?: array<string>,
     *     content: string,
     *     listable?: bool,
     *     password?: string,
     *     expiration?: string,
     *     destroy_on_open?: bool
     * } $data
     */
    public function create(array $data, ?User $user): Paste
    {
        if ($user)
        {
            $data['user_id'] = $user->id;
        }
        if (isset($data['password']))
        {
            $data['password'] = Hash::make($data['password']);
        }
        return Paste::create($data);
    }

    /**
     * @param array{
     *     syntax_highlight_id?: string,
     *     title?: string,
     *     tags?: array<string>,
     *     content?: string,
     *     listable?: bool,
     *     password?: string,
     *     expiration?: string,
     *     destroy_on_open?: bool
     * } $data
     */
    public function edit(Paste $paste, array $data): Paste
    {
        $data = array_filter($data, fn ($value) => $value !== null);
        $paste->update($data);
        return $paste;
    }

    public function isLikedByUser(Paste $paste, User $user): bool
    {
        return $paste->likes()->where('user_id', $user->id)->exists();
    }

    public function toggleLike(Paste $paste, User $user): bool
    {
        $existingLike = $this->isLikedByUser($paste, $user);

        if ($existingLike)
        {
            $paste->likes()->where('user_id', $user->id)->delete();
        }
        else
        {
            $paste->likes()->create(['user_id' => $user->id]);
        }

        return !$existingLike;
    }

    public function delete(Paste $paste): void
    {
        $paste->delete();
    }
}
