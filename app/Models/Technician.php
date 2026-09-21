<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'email',
        'address',
        'specialization',
        'commission_percent',
        'salary',
        'status',
    ];

    protected $casts = [
        'commission_percent' => 'decimal:2',
        'salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repairJobs()
    {
        return $this->hasMany(RepairJob::class);
    }

    public function getActiveRepairsCountAttribute(): int
    {
        return $this->repairJobs()->whereNotIn('status', ['Delivered', 'Cancelled'])->count();
    }

    public function getCompletedRepairsCountAttribute(): int
    {
        return $this->repairJobs()->where('status', 'Delivered')->count();
    }
}
