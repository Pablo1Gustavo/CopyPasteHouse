<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class Tag extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'color',
        'is_public',
    ];

    /**
     * Get the user who created this tag
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all pastes that have this tag
     */
    public function pastes(): BelongsToMany
    {
        return $this->belongsToMany(Paste::class, 'paste_tag')
            ->withTimestamps();
    }

    /**
     * Get tags ordered by usage count
     */
    public static function getPopularTags(int $limit = 10)
    {
        return self::withCount('pastes')
            ->orderByDesc('pastes_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get tags by paste count
     */
    public static function getTagsByUsage()
    {
        return self::withCount('pastes')
            ->orderByDesc('pastes_count')
            ->get();
    }
}
