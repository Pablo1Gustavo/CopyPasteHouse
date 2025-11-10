<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\{SyntaxHighlight};
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePasteRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $syntaxHighlightExists = Rule::exists(SyntaxHighlight::class, 'id');

        return [
            'syntax_highlight_id' => ['nullable', $syntaxHighlightExists],
            'title'               => ['required', 'string', 'max:50'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['string', 'max:50'],
            'content'             => ['required', 'string', 'max:30000'],
            'listable'            => ['boolean'],
            'password'            => ['nullable', 'string', 'min:3', 'max:70'],
            'expiration'          => ['nullable', 'date_format:Y-m-d H:i'],
            'destroy_on_open'     => ['boolean'],
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
