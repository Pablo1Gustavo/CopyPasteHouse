<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSettings extends Model
{
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'timezone',
        'language',
        'theme'
    ];
    public $timestamps = false;
    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
