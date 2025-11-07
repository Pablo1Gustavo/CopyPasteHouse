<?php
declare(strict_types=1);
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CommaSeparatedStringListCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes): ?array
    {
        return is_null($value) ? null : explode(',', $value) ;
    }

    public function set(Model $model, string $key, $value, array $attributes): ?string
    {
        return is_array($value) ? implode(',', $value) : null;
    }
}
