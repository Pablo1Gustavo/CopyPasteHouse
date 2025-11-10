<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentLike extends Model
{
    use HasFactory, HasUuids;

    const CREATED_AT = 'liked_at';
    const UPDATED_AT = null;

    protected $fillable = ['comment_id', 'user_id', 'liked_at'];

    protected $casts = [
        'liked_at' => 'datetime',
    ];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(PasteComment::class, 'comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get recent likes
     */
    public static function getRecentLikes(int $limit = 20)
    {
        return self::with(['comment', 'user'])
            ->orderByDesc('liked_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get likes by user
     */
    public static function getLikesByUser(string $userId)
    {
        return self::with('comment')
            ->where('user_id', $userId)
            ->orderByDesc('liked_at')
            ->get();
    }
}
