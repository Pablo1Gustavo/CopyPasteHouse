<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePasteCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paste_id' => 'required|exists:pastes,id',
            'content' => 'required|string|max:10000',
            'syntax_highlight_id' => 'nullable|exists:syntax_highlights,id',
        ];
    }
}
