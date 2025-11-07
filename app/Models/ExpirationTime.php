<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpirationTime extends Model
{
    use HasFactory;

    protected $primaryKey = 'minutes';
    protected $fillable = ['minutes', 'label'];
    protected $keyType = 'int';

    protected $casts = [
        'minutes' => 'integer',
    ];
    public $timestamps = false;
    public $incrementing = false;
}
