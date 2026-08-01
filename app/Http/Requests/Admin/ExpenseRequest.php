<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'payment_method' => 'required|in:cash,card,bank',
            'status' => 'required|in:paid,pending,cancelled',
        ];
    }
}