<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'syntax_highlight_id' => 'sometimes|exists:syntax_highlights,id',
            'title' => 'sometimes|string|max:50',
            'tags' => 'nullable|string|max:255',
            'content' => 'sometimes|string|max:512000',
            'listable' => 'sometimes|boolean',
            'password' => 'nullable|string|min:8|max:255',
            'expiration' => 'nullable|integer|min:10|max:525600', // 10 minutes to 1 year in minutes
            'destroy_on_open' => 'sometimes|boolean',
        ];
    }
}
