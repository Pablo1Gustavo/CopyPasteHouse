<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasteLike extends Model
{
    use HasFactory, HasUuids;

    const CREATED_AT = 'liked_at';
    const UPDATED_AT = null;

    protected $fillable = ['paste_id', 'user_id', 'liked_at'];

    protected $casts = [
        'liked_at' => 'datetime',
    ];

    public function paste(): BelongsTo
    {
        return $this->belongsTo(Paste::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get most liked pastes
     */
    public static function getMostLikedPastes(int $limit = 10)
    {
        return self::select('paste_id', \DB::raw('count(*) as likes_count'))
            ->groupBy('paste_id')
            ->orderByDesc('likes_count')
            ->limit($limit)
            ->with('paste')
            ->get();
    }

    /**
     * Get recent likes
     */
    public static function getRecentLikes(int $limit = 20)
    {
        return self::with(['paste', 'user'])
            ->orderByDesc('liked_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get likes by user
     */
    public static function getLikesByUser(string $userId)
    {
        return self::with('paste')
            ->where('user_id', $userId)
            ->orderByDesc('liked_at')
            ->get();
    }

    /**
     * Get likes by paste
     */
    public static function getLikesByPaste(string $pasteId)
    {
        return self::with('user')
            ->where('paste_id', $pasteId)
            ->orderByDesc('liked_at')
            ->get();
    }

    /**
     * Get likes count for a paste
     */
    public static function getLikesCount(string $pasteId): int
    {
        return self::where('paste_id', $pasteId)->count();
    }
}
