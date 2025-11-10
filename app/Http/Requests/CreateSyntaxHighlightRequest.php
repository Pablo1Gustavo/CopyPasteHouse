<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SyntaxHighlight;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSyntaxHighlightRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $uniqueSyntaxHighlightName = Rule::unique(SyntaxHighlight::class, 'name');
        $uniqueSyntaxHighlightExtension = Rule::unique(SyntaxHighlight::class, 'extension');

        return [
            'name' => ['required', 'string', 'max:35', $uniqueSyntaxHighlightName],
            'extension' => ['required', 'string', 'max:25', $uniqueSyntaxHighlightExtension],
        ];
    }
}
