<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inventoriable_id',
        'inventoriable_type',
        'type',
        'quantity',
        'price',
        'total_price',
        'remarks',
        'reference_id',
        'reference_type',
        'created_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function inventoriable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}