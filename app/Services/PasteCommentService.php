<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotOwner;
use App\Models\{Paste, PasteComment, User};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PasteCommentService
{
    /**
     * @return Collection<PasteComment>
     */
    public function list(Paste $paste): Collection
    {
        return $paste->comments()
            ->with(['user', 'syntaxHighlight'])
            ->withCount('likes')
            ->get();
    }

    /**
     * @param array{
     *     content: string,
     *     syntax_highlight_id?: string
     * } $data
     */
    public function create(Paste $paste, User $user, array $data): PasteComment
    {
        $data['user_id'] = $user->id;
        return $paste->comments()->create($data);
    }

    public function validateAuthenticatedUserOwnership(PasteComment $comment)
    {
        if (Auth::id() != $comment->user_id)
        {
            throw new NotOwner;
        }
    }

    /**
     * @param array{
     *     content: string,
     *     syntax_highlight_id: string
     * } $data
     */
    public function edit(PasteComment $comment, array $data): PasteComment
    {
        $comment->update($data);
        return $comment;
    }

    public function delete(PasteComment $comment): void
    {
        $comment->delete();
    }

    public function isLikedByUser(PasteComment $comment, User $user): bool
    {
        return $comment->likes()->where('user_id', $user->id)->exists();
    }

    public function toggleLike(PasteComment $pasteComment, User $user): array
    {
        $existingLike = $this->isLikedByUser($pasteComment, $user);

        if ($existingLike)
        {
            $pasteComment->likes()->where('user_id', $user->id)->delete();
        }
        else
        {
            $pasteComment->likes()->create(['user_id' => $user->id]);
        }

        return [
            'liked' => !$existingLike,
            'count' => $pasteComment->likes()->count()
        ];
    }
}
