<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sale_id',
        'sellable_id',
        'sellable_type',
        'quantity',
        'selling_price',
        'purchase_price',
        'total',
        'profit',
        'imei',
        'serial_number'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'total' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function sellable()
    {
        return $this->morphTo();
    }
}