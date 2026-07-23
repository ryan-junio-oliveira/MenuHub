<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer.name' => ['required_without:customer_id', 'string'],
            'customer.phone' => ['required_without:customer_id', 'string'],
            'customer.address' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.dish_id' => ['nullable', 'exists:dishes,id'],
            'items.*.dish_name' => ['required', 'string'],
            'items.*.size' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required', 'numeric', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'delivery_fee' => ['numeric', 'min:0'],
            'discount' => ['numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'delivery_type' => ['string', 'in:delivery,pickup'],
            'delivery_address' => ['nullable', 'string'],
        ];
    }
}
