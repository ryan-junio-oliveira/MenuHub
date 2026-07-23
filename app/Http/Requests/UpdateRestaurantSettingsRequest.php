<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email'],
            'phone' => ['sometimes', 'nullable', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'pix_key' => ['sometimes', 'nullable', 'string'],
            'whatsapp_number' => ['sometimes', 'nullable', 'string'],
            'delivery_fee' => ['sometimes', 'numeric', 'min:0'],
            'minimum_order' => ['sometimes', 'numeric', 'min:0'],
            'opening_hours' => ['sometimes', 'nullable', 'json'],
            'logo' => ['sometimes', 'nullable', 'image', 'max:2048'],
            'cover' => ['sometimes', 'nullable', 'image', 'max:2048'],
        ];
    }
}
