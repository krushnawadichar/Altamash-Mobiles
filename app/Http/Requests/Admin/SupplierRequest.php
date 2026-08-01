<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplierId = $this->route('supplier') ? $this->route('supplier')->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplierId,
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }
}