<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'dishes' => ['nullable', 'array'],
            'dishes.*' => ['exists:dishes,id'],
            'price_small' => ['nullable', 'array'],
            'price_small.*' => ['nullable', 'numeric', 'min:0'],
            'price_medium' => ['nullable', 'array'],
            'price_medium.*' => ['nullable', 'numeric', 'min:0'],
            'price_large' => ['nullable', 'array'],
            'price_large.*' => ['nullable', 'numeric', 'min:0'],
            'is_published' => ['boolean'],
        ];
    }
}
