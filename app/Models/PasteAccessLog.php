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
}
