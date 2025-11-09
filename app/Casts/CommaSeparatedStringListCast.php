<?php
declare(strict_types=1);
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommaSeparatedStringListCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes): ?array
    {
        if (empty($value))
        {
            return null;
        }
        $array = explode(',', $value);
        return array_slice($array, 1, -1);
    }

    public function set(Model $model, string $key, $value, array $attributes): ?string
    {
        if (!is_array($value) || empty($value))
        {
            return null;
        }
        sort($value);
        $value = array_filter($value, fn ($item) => !empty($item));
        $value = array_map(Str::slug(...), $value);
        $value = array_unique($value);
        return ',' . implode(',', $value) . ',';
    }
}
