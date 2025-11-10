<?php declare(strict_types=1);

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ListPastesRequest extends FormRequest
{
    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'             => ['nullable', 'exists:users,id'],
            'syntax_highlight_id' => ['nullable', 'exists:syntax_highlights,id'],
            'title'               => ['nullable', 'string', 'max:50'],
            'tags'                => ['nullable', 'array'],
            'tags.*'              => ['string', 'max:50'],
            'created_after'       => ['nullable', 'date'],
            'created_before'      => ['nullable', 'date'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->replace([
            'created_after'  => Carbon::make($this->input('created_after')),
            'created_before' => Carbon::make($this->input('created_before')),
        ]);
    }
}
