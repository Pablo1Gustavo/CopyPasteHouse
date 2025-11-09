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

    public function delete(SyntaxHighlight $syntaxHighlight): void
    {
        $syntaxHighlight->delete();
    }
}
