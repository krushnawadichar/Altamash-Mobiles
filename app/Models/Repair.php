<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $fillable = [
        'repair_code',
        'customer_name',
        'customer_phone',
        'device_name',
        'problem_description',
        'received_date',
        'expected_delivery',
        'estimated_cost',
        'final_cost',
        'advance_payment',
        'material_cost',
        'service_charge',
        'paid_amount',
        'due_amount',
        'profit',
        'payment_status',
        'payment_method',
        'status',
        'technician_notes',
        'handled_by',
    ];

    protected $casts = [
        'received_date' => 'date',
        'expected_delivery' => 'date',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function items()
    {
        return $this->hasMany(RepairItem::class);
    }

    /**
     * Get effective customer bill amount (final_cost if set, otherwise estimated_cost).
     */
    public function getEffectiveTotalAttribute(): float
    {
        return (float) ($this->final_cost > 0 ? $this->final_cost : $this->estimated_cost);
    }
}
