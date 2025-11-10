<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyntaxHighlight extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = ['label', 'value'];
    public $timestamps = false;

    public function pastes(): HasMany
    {
        return $this->hasMany(Paste::class);
    }
}
