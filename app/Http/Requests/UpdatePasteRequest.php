<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\SyntaxHighlight;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePasteRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $syntaxHighlightExists = Rule::exists(SyntaxHighlight::class, 'id');

        return [
            'syntax_highlight_id' => ['nullable', $syntaxHighlightExists],
            'title'               => ['nullable', 'string', 'max:50'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['nullable', 'max:50'],
            'content'             => ['nullable', 'string', 'max:512000'],
            'listable'            => ['nullable', 'boolean'],
            'password'            => ['nullable', 'string', 'min:8', 'max:255'],
            'expiration'          => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.max' => 'The content may not be greater than 500KB.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'expiration' => Carbon::make($this->input('expiration')),
        ]);
    }
}
