<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SyntaxHighlight;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSyntaxHighlightRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $syntaxHighlightId = $this->route('syntaxHighlight')?->id;

        $uniqueSyntaxHighlightName = Rule::unique(SyntaxHighlight::class, 'name')
            ->ignore($syntaxHighlightId);
        $uniqueSyntaxHighlightExtension = Rule::unique(SyntaxHighlight::class, 'extension')
            ->ignore($syntaxHighlightId);

        return [
            'name'      => ['nullable', 'string', 'max:35', $uniqueSyntaxHighlightName],
            'extension' => ['nullable', 'string', 'max:25', $uniqueSyntaxHighlightExtension],
        ];
    }
}
