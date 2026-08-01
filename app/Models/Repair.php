<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repair extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'repair_number',
        'customer_id',
        'customer_name',
        'customer_mobile',
        'device_name',
        'imei',
        'issue',
        'accessories_received',
        'estimated_cost',
        'advance_paid',
        'remaining_amount',
        'engineer_notes',
        'repair_status_id',
        'receive_date',
        'delivery_date',
        'payment_status',
        'images',
        'documents',
        'created_by'
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'advance_paid' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'receive_date' => 'date',
        'delivery_date' => 'date',
        'images' => 'array',
        'documents' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function repairStatus()
    {
        return $this->belongsTo(RepairStatus::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePending($query)
    {
        return $query->whereHas('repairStatus', function ($q) {
            $q->where('name', 'Pending');
        });
    }

    public function scopeCompleted($query)
    {
        return $query->whereHas('repairStatus', function ($q) {
            $q->where('name', 'Completed');
        });
    }
}