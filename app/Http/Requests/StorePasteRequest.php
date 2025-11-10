<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePasteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'syntax_highlight_id' => 'required|exists:syntax_highlights,id',
            'title' => 'required|string|max:50',
            'tags' => 'nullable|string|max:255', // Keep for backward compatibility
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'content' => 'required|string|max:512000', // 500KB max
            'listable' => 'boolean',
            'password' => 'nullable|string|min:8|max:255',
            'expiration' => 'nullable|integer|min:10|max:525600', // 10 minutes to 1 year in minutes
            'destroy_on_open' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'expiration.min' => 'Expiration must be at least 10 minutes',
            'expiration.max' => 'Expiration cannot exceed 1 year (525600 minutes)',
            'content.max' => 'Content cannot exceed 500KB',
        ];
    }
}
