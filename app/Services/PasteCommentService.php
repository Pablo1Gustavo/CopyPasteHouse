<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\{CommentLike, Paste, PasteComment, User};
use Illuminate\Support\Collection;

class PasteCommentService
{
    /**
     * @return Collection<PasteComment>
     */
    public function list(Paste $paste): Collection
    {
        return $paste->comments()->get();
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

    /**
     * @param array{
     *     content?: string,
     *     syntax_highlight_id?: string
     * } $data
     */
    public function edit(PasteComment $comment, array $data): PasteComment
    {
        $data = array_filter($data, fn ($value) => $value !== null);
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

    public function toggleLike(PasteComment $pasteComment, User $user): bool
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

        return !$existingLike;
    }
}
