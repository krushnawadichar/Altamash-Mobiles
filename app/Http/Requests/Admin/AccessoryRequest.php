<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AccessoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accessoryId = $this->route('accessory') ? $this->route('accessory')->id : null;

        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'minimum_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ];
    }
}