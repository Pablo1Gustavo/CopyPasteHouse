<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class PasteComment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'paste_id',
        'user_id',
        'content',
        'syntax_highlight_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function paste(): BelongsTo
    {
        return $this->belongsTo(Paste::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function syntaxHighlight(): BelongsTo
    {
        return $this->belongsTo(SyntaxHighlight::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class, 'comment_id');
    }
}
