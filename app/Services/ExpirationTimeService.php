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
     * Get a single expiration time by ID
     */
    public function show(string $id): ?ExpirationTime
    {
        return ExpirationTime::find($id);
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

    public function delete(string $id): bool
    {
        $expirationTime = ExpirationTime::find($id);
        
        if (!$expirationTime) {
            return false;
        }
        
        $expirationTime->delete();
        return true;
    }
}
