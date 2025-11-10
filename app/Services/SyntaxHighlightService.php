<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\SyntaxHighlight;
use Illuminate\Support\Collection;

class SyntaxHighlightService
{
    /**
     * @return Collection<SyntaxHighlight>
     */
    public function list(): Collection
    {
        return SyntaxHighlight::all();
    }

    /**
     * Get a single syntax highlight by ID
     */
    public function show(string $id): ?SyntaxHighlight
    {
        return SyntaxHighlight::find($id);
    }

    /**
     * @param array{extension: string, name: string} $data
     */
    public function create(array $data): SyntaxHighlight
    {
        return SyntaxHighlight::create($data);
    }

    /**
     * @param array{extension?: string, name?: string} $data
     */
    public function edit(SyntaxHighlight $syntaxHighlight, array $data): SyntaxHighlight
    {
        $data = array_filter($data, fn ($value) => $value !== null);
        $syntaxHighlight->update($data);
        return $syntaxHighlight;
    }

    public function delete(string $id): bool
    {
        $syntaxHighlight = SyntaxHighlight::find($id);
        
        if (!$syntaxHighlight) {
            return false;
        }
        
        $syntaxHighlight->delete();
        return true;
    }
}
