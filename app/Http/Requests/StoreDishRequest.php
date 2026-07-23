<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dish_category_id' => ['required', 'exists:dish_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_small' => ['nullable', 'numeric', 'min:0'],
            'price_medium' => ['nullable', 'numeric', 'min:0'],
            'price_large' => ['nullable', 'numeric', 'min:0'],
            'is_gourmet' => ['boolean'],
            'max_selections' => ['integer', 'min:1'],
            'is_available' => ['boolean'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
