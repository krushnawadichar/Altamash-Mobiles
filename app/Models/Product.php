<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'type', // mobile, accessory, spare_part
        'brand_id',
        'category_id',
        'sub_category_id',
        'model_no',
        'barcode',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'mrp',
        'tax_percent',
        'min_stock',
        'current_stock',
        'image',
        'description',
        'status',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'mrp' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'min_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function availableSerials()
    {
        return $this->hasMany(ProductSerial::class)->where('status', 'available');
    }

    public function soldSerials()
    {
        return $this->hasMany(ProductSerial::class)->where('status', 'sold');
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function isMobile(): bool
    {
        return $this->type === 'mobile';
    }

    public function isAccessory(): bool
    {
        return $this->type === 'accessory';
    }

    public function isSparePart(): bool
    {
        return $this->type === 'spare_part';
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'Out of Stock';
        }

        if ($this->current_stock <= $this->min_stock) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function getStockBadgeClassAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'bg-danger';
        }

        if ($this->current_stock <= $this->min_stock) {
            return 'bg-warning text-dark';
        }

        return 'bg-success';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%")
              ->orWhere('model_no', 'like', "%{$term}%")
              ->orWhereHas('brand', function ($b) use ($term) {
                  $b->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('category', function ($c) use ($term) {
                  $c->where('name', 'like', "%{$term}%");
              })
              ->orWhereHas('serials', function ($s) use ($term) {
                  $s->where('imei_1', 'like', "%{$term}%")
                    ->orWhere('imei_2', 'like', "%{$term}%")
                    ->orWhere('serial_no', 'like', "%{$term}%");
              });
        });
    }
}
