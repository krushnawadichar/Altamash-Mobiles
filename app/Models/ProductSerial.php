<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'imei_1',
        'imei_2',
        'serial_no',
        'color',
        'ram',
        'storage',
        'warranty_months',
        'purchase_price',
        'selling_price',
        'purchase_item_id',
        'sale_item_id',
        'status', // available, sold, under_repair, damaged, returned
        'sold_at',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'warranty_months' => 'integer',
        'sold_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    public function isSold(): bool
    {
        return $this->status === 'sold';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-success',
            'sold' => 'bg-secondary',
            'under_repair' => 'bg-warning text-dark',
            'damaged' => 'bg-danger',
            'returned' => 'bg-info text-dark',
            default => 'bg-light text-dark',
        };
    }
}
