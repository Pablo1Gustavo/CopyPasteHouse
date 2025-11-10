<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasteAccessLog extends Model
{
    use HasFactory, HasUuids;

    const CREATED_AT = 'access_date';
    const UPDATED_AT = null;

    protected $fillable = [
        'paste_id',
        'user_id',
        'ip',
        'user_agent',
        'access_date'
    ];

    protected $casts = [
        'access_date' => 'datetime',
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
     * Get most viewed pastes
     */
    public static function getMostViewedPastes(int $limit = 10)
    {
        return self::select('paste_id', \DB::raw('count(*) as views_count'))
            ->groupBy('paste_id')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->with('paste')
            ->get();
    }

    /**
     * Get access logs by paste
     */
    public static function getAccessByPaste(string $pasteId)
    {
        return self::with('user')
            ->where('paste_id', $pasteId)
            ->orderByDesc('access_date')
            ->get();
    }

    /**
     * Get access logs by user
     */
    public static function getAccessByUser(string $userId)
    {
        return self::with('paste')
            ->where('user_id', $userId)
            ->orderByDesc('access_date')
            ->get();
    }

    /**
     * Get unique visitors count for a paste
     */
    public static function getUniqueVisitorsCount(string $pasteId): int
    {
        return self::where('paste_id', $pasteId)
            ->distinct('ip')
            ->count('ip');
    }

    /**
     * Get access count for a paste
     */
    public static function getAccessCount(string $pasteId): int
    {
        return self::where('paste_id', $pasteId)->count();
    }
}
