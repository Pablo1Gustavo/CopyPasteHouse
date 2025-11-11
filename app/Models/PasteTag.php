<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PasteTag extends Pivot
{
    use HasUuids;

    protected $fillable = ['paste_id', 'tag_id'];
}
