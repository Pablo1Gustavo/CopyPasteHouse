<?php declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpirationTime extends Model
{
    protected $primaryKey = 'minutes';
    protected $fillable = ['minutes', 'label'];
    protected $keyType = 'int';

    protected $casts = [
        'minutes' => 'integer',
    ];
    public $timestamps = false;
    public $incrementing = false;
}
