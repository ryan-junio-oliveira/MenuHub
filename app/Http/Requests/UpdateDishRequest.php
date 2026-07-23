<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dish_category_id' => ['sometimes', 'required', 'exists:dish_categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price_small' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_medium' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'price_large' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'is_gourmet' => ['sometimes', 'boolean'],
            'max_selections' => ['sometimes', 'integer', 'min:1'],
            'is_available' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'image' => ['sometimes', 'nullable', 'image', 'max:2048'],
        ];
    }
}
