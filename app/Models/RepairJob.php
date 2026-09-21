<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_no',
        'customer_id',
        'customer_name',
        'customer_mobile',
        'brand_id',
        'model_name',
        'imei',
        'serial_no',
        'color',
        'problem_complaint',
        'physical_condition',
        'accessories_received',
        'estimated_cost',
        'final_cost',
        'advance_amount',
        'paid_amount',
        'due_amount',
        'technician_id',
        'status',
        'received_date',
        'expected_delivery_date',
        'completed_date',
        'delivered_date',
        'warranty_days',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'received_date' => 'datetime',
        'expected_delivery_date' => 'date',
        'completed_date' => 'datetime',
        'delivered_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'warranty_days' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function partsUsed()
    {
        return $this->hasMany(RepairPartUsed::class);
    }

    public function payments()
    {
        return $this->hasMany(RepairPayment::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(RepairStatusLog::class)->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Received' => 'bg-secondary',
            'Diagnosis Pending' => 'bg-info text-dark',
            'Under Diagnosis' => 'bg-primary',
            'Estimate Given' => 'bg-warning text-dark',
            'Customer Approval Pending' => 'bg-warning text-dark',
            'Approved' => 'bg-info text-dark',
            'Repairing' => 'bg-primary',
            'Waiting for Parts' => 'bg-danger',
            'Repair Completed' => 'bg-success',
            'Ready for Delivery' => 'bg-teal text-white',
            'Delivered' => 'bg-success',
            'Cancelled' => 'bg-dark',
            default => 'bg-secondary',
        };
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Delivered', 'Cancelled']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Delivered');
    }

    public function scopeReadyForDelivery($query)
    {
        return $query->where('status', 'Ready for Delivery');
    }
}
