<?php
declare(strict_types=1);
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class CommaSeparatedStringListCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes): ?array
    {
        if (is_null($value))
        {
            return null;
        }
        $array = explode(',', $value);
        return array_slice($array, 1, -1);
    }

    public function set(Model $model, string $key, $value, array $attributes): ?string
    {
        if (!is_array($value))
        {
            return null;
        }
        sort($value);
        return ',' . implode(',', $value) . ',';
    }
}
