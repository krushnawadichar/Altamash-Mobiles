<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'repair_job_id',
        'from_status',
        'to_status',
        'notes',
        'created_by',
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
