<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SyntaxHighlight;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentOnPasteRequest extends FormRequest
{
    public function rules(): array
    {
        $syntaxHighlightExists = Rule::exists(SyntaxHighlight::class, 'id');

        return [
            'content'             => ['required', 'string', 'max:10000'],
            'syntax_highlight_id' => ['nullable', 'uuid', $syntaxHighlightExists],
        ];
    }
}
