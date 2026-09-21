<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function repairJobs()
    {
        return $this->hasMany(RepairJob::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('grand_total');
    }

    public function getTotalPaidAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('paid_amount') + $this->payments()->whereNull('sale_id')->sum('amount');
    }

    public function getTotalDueAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('due_amount');
    }
}
