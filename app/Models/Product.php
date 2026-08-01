<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'imei',
        'category_id',
        'brand_id',
        'supplier_id',
        'unit_id',
        'product_type_id',
        'mobile_company_id',
        'purchase_price',
        'selling_price',
        'gst_percentage',
        'tax_amount',
        'color',
        'storage',
        'ram',
        'description',
        'minimum_stock',
        'current_stock',
        'image',
        'status',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function mobileCompany()
    {
        return $this->belongsTo(MobileCompany::class);
    }

    public function purchaseDetails()
    {
        return $this->morphMany(PurchaseDetail::class, 'purchasable');
    }

    public function saleDetails()
    {
        return $this->morphMany(SaleDetail::class, 'sellable');
    }

    public function inventories()
    {
        return $this->morphMany(Inventory::class, 'inventoriable');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }
}