<?php declare(strict_types=1);
namespace App\Models;

use App\Casts\CommaSeparatedStringListCast;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Paste extends Model
{
    use HasFactory, HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'syntax_highlight_id',
        'user_id',
        'title',
        'tags',
        'content',
        'listable',
        'password',
        'expiration',
        'destroy_on_open'
    ];
    protected $casts = [
        'tags'            => CommaSeparatedStringListCast::class,
        'created_at'      => 'datetime',
        'expiration'      => 'datetime',
        'listable'        => 'boolean',
        'destroy_on_open' => 'boolean'
    ];

    public function syntaxHighlight(): BelongsTo
    {
        return $this->belongsTo(SyntaxHighlight::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PasteLike::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(PasteAccessLog::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PasteComment::class);
    }
}
