<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasteCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'sometimes|string|max:10000',
            'syntax_highlight_id' => 'nullable|exists:syntax_highlights,id',
        ];
    }
}
