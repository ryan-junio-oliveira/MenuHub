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
            'items' => ['nullable', 'array'],
            'items.*.dish_id' => ['nullable', 'exists:dishes,id'],
            'items.*.daily_menu_item_id' => ['nullable', 'exists:daily_menu_items,id'],
            'items.*.dish_name' => ['required_with:items', 'string'],
            'items.*.size' => ['nullable', 'string'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:0'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.subtotal' => ['required_with:items', 'numeric', 'min:0'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'delivery_type' => ['nullable', 'string', 'in:delivery,pickup'],
            'delivery_address' => ['nullable', 'string'],
        ];
    }
}
