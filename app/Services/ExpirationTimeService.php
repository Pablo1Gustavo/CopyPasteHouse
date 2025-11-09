<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ExpirationTime;
use Illuminate\Support\Collection;

class ExpirationTimeService
{
    /**
     * @return Collection<ExpirationTime>
     */
    public function list(): Collection
    {
        return ExpirationTime::all();
    }

    /**
     * @param array{minutes: int, label: string} $data
     */
    public function create(array $data): ExpirationTime
    {
        return ExpirationTime::create($data);
    }

    /**
     * @param array{minutes?: int, label?: string} $data
     */
    public function edit(ExpirationTime $expirationTime, array $data): ExpirationTime
    {
        $data = array_filter($data, fn ($value) => $value !== null);
        $expirationTime->update($data);
        return $expirationTime;
    }

    public function delete(ExpirationTime $expirationTime): void
    {
        $expirationTime->delete();
    }
}
