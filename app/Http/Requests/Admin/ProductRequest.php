<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_id' => 'required|exists:units,id',
            'product_type_id' => 'required|exists:product_types,id',
            'mobile_company_id' => 'nullable|exists:mobile_companies,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'color' => 'nullable|string|max:50',
            'storage' => 'nullable|string|max:50',
            'ram' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'current_stock' => 'nullable|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ];
    }
}