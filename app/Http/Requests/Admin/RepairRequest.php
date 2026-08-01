<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RepairRequest extends FormRequest
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
            'device_name' => 'required|string|max:255',
            'imei' => 'nullable|string|max:50',
            'issue' => 'required|string|max:1000',
            'accessories_received' => 'nullable|string|max:500',
            'estimated_cost' => 'nullable|numeric|min:0',
            'advance_paid' => 'nullable|numeric|min:0',
            'engineer_notes' => 'nullable|string|max:1000',
            'repair_status_id' => 'required|exists:repair_statuses,id',
            'receive_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:receive_date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ];
    }
}