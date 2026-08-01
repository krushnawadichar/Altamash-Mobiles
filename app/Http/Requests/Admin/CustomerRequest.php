<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customerId,
            'phone' => 'required|string|max:20',
            'alternative_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }
}