<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_category_id',
        'title',
        'amount',
        'expense_date',
        'description',
        'receipt',
        'payment_method',
        'status',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}