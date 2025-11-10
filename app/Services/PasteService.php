<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\{NotOwner, WrongPastePassword};
use App\Models\{Paste, User};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\{Collection, Str};
use Illuminate\Support\Facades\{Auth, DB, Hash};

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
      
        if (isset($paste->password) && !Hash::check($password, $paste->password))
        {
            throw new WrongPastePassword;
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
                'user_id'    => $user?->id,
                'ip'         => $ipAddress,
                'user_agent' => $userAgent,
            ]);
            if ($paste->destroy_on_open)
            {
                $paste->delete();
            }
        });

        $paste->makeHidden('password');
        $paste
            ->makeHidden('password')
            ->load([
                'syntaxHighlight',
                'user',
            ])
            ->loadCount([
                'likes',
                'accessLogs AS access_count',
            ]);

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

    public function validateAuthenticatedUserOwnership(Paste $comment)
    {
        if (Auth::id() != $comment->user_id)
        {
            throw new NotOwner;
        }
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
        $this->validateAuthenticatedUserOwnership($paste);

        $data = array_filter($data, fn ($value) => $value !== null);
        $paste->update($data);
        return $paste;
    }

    public function isLikedByUser(Paste $paste, User $user): bool
    {
        return $paste->likes()->where('user_id', $user->id)->exists();
    }

    public function toggleLike(Paste $paste, User $user): array
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

        return [
            'liked' => !$existingLike,
            'count' => $paste->likes()->count()
        ];
    }

    public function delete(Paste $paste): void
    {
        $this->validateAuthenticatedUserOwnership($paste);
        $paste->delete();
    }
}
