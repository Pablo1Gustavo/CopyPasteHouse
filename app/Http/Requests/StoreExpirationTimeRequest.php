<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpirationTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => 'required|string|max:50',
            'minutes' => 'required|integer|min:10|max:525600', // 10 minutes to 1 year
        ];
    }

    public function messages(): array
    {
        return [
            'minutes.min' => 'Expiration time must be at least 10 minutes',
            'minutes.max' => 'Expiration time cannot exceed 1 year (525600 minutes)',
        ];
    }
}
