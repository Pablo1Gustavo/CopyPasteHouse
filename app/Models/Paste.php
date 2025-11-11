<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};

class Paste extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    const UPDATED_AT = null;

    protected $fillable = [
        'syntax_highlight_id',
        'user_id',
        'title',
        'content',
        'listable',
        'password',
        'expiration',
        'destroy_on_open'
    ];
    protected $hidden = [
        'password'
    ];
    protected $casts = [
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

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'paste_tag')
            ->withTimestamps()
            ->using(PasteTag::class);
    }
}
