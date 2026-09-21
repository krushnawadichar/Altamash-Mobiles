<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'payment_date',
        'amount',
        'payment_type', // advance, partial, final
        'payment_method', // cash, upi, card, bank_transfer
        'transaction_ref',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function repairJob()
    {
        return $this->belongsTo(RepairJob::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
