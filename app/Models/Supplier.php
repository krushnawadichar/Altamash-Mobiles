<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'mobile',
        'email',
        'address',
        'gst_number',
        'opening_balance',
        'current_balance',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()->sum('grand_total');
    }

    public function getTotalPaidAttribute()
    {
        return $this->purchases()->sum('paid_amount') + $this->payments()->whereNull('purchase_id')->sum('amount');
    }

    public function getTotalDueAttribute()
    {
        return $this->purchases()->sum('due_amount');
    }
}
