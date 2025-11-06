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
}
