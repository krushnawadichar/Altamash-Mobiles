<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $fillable = [
        'repair_code', 'customer_name', 'customer_phone', 'device_name',
        'problem_description', 'received_date', 'expected_delivery',
        'estimated_cost', 'final_cost', 'advance_payment', 'status',
        'technician_notes', 'handled_by'
    ];

    protected $casts = [
        'received_date' => 'date',
        'expected_delivery' => 'date',
    ];

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
