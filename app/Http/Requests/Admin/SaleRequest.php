<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required_if:customer_id,null|string|max:255',
            'customer_mobile' => 'required_if:customer_id,null|string|max:20',
            'sale_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,online',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.type' => ['required', Rule::in(['product', 'accessory'])],
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please add at least one item to sale.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}