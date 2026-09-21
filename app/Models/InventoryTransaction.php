<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_serial_id',
        'transaction_type',
        'reference_id',
        'reference_type',
        'quantity',
        'before_stock',
        'after_stock',
        'unit_cost',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'quantity' => 'integer',
        'before_stock' => 'integer',
        'after_stock' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function serial()
    {
        return $this->belongsTo(ProductSerial::class, 'product_serial_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
