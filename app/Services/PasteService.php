<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\WrongPastePassword;
use App\Models\{Paste, User};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

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
                $q->where('title', 'like', '%' . $filters['title'] . '%')
            )
            ->when(isset($filters['created_after']), fn (Builder $q) =>
                $q->where('created_at', '>=', $filters['created_after'])
            )
            ->when(isset($filters['created_before']), fn (Builder $q) =>
                $q->where('created_at', '<=', $filters['created_before'])
            );

        if (isset($filters['tags']))
        {
            $tags = $filters['tags'];
            $tags = array_filter($tags, fn ($item) => !empty($item));
            $tags = array_map(static fn ($item) => Str::slug($item), $tags);
            $tags = array_unique($tags);

            foreach ($tags as $tag)
            {
                $query->where('tags', 'LIKE', '%,' . $tag . ',%');
            }
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

        if ($paste->password)
        {
            $senhaValida = $password !== null && Hash::check($password, $paste->password);
            if (!$senhaValida)
            {
                throw new WrongPastePassword;
            }
        }

        $paste->loadMissing([
            'syntaxHighlight',
            'user',
        ])->loadCount([
            'likes',
            'accessLogs AS access_count',
        ]);

        $destroyedPaste = null;

        DB::transaction(function () use (&$destroyedPaste, $user, $paste, $ipAddress, $userAgent)
        {
            $paste->accessLogs()->create([
                'user_id'     => $user?->id,
                'ip'          => $ipAddress,
                'user_agent'  => $userAgent,
                'access_date' => now(),
            ]);

            if ($paste->destroy_on_open)
            {
                $destroyedPaste = $paste->replicate();
                $destroyedPaste->setRelations($paste->getRelations());
                $destroyedPaste->setAttribute('destroyed', true);
                $paste->delete();
            }
        });

        $paste->makeHidden('password');

        if ($destroyedPaste)
        {
            $destroyedPaste->makeHidden('password');
            $destroyedPaste->setAttribute('access_count', ($paste->access_count ?? 0) + 1);
            $destroyedPaste->setAttribute('likes_count', $paste->likes_count ?? $paste->likes()->count());
            return $destroyedPaste;
        }

        $paste->setAttribute('access_count', ($paste->access_count ?? 0) + 1);

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
        // Manually cascade deletes because SQLite ignores on delete cascade when altering tables post-hoc.
        DB::transaction(function () use ($paste)
        {
            $paste->accessLogs()->delete();
            $paste->likes()->delete();

            $paste->comments()->each(function ($comment)
            {
                $comment->likes()->delete();
                $comment->forceDelete();
            });

            $paste->delete();
        });
    }
}
