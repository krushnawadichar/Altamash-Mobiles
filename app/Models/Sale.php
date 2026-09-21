<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'customer_id',
        'customer_name',
        'customer_mobile',
        'sale_date',
        'subtotal',
        'discount_type',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'due_amount',
        'change_amount',
        'payment_status', // paid, partial, due
        'payment_method', // cash, upi, card, bank_transfer, mixed, credit
        'status', // completed, cancelled, returned
        'notes',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPaymentBadgeClassAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'bg-success',
            'partial' => 'bg-warning text-dark',
            'due' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'returned' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    public function getProfitAttribute(): float
    {
        $totalCost = $this->items->sum(function ($item) {
            return $item->unit_cost * $item->quantity;
        });
        return (float) ($this->grand_total - $totalCost);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)->whereYear('sale_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('sale_date', now()->year);
    }
}
