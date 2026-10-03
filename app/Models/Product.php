<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'sku', 'barcode', 'category_id', 'subcategory_id', 'brand_id',
        'model_number', 'short_description', 'description', 'purchase_price',
        'selling_price', 'mrp', 'discount', 'tax', 'stock_quantity', 'min_stock',
        'warranty_period', 'weight', 'dimensions', 'status', 'featured',
        'new_arrival', 'best_seller', 'requires_serial'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }
}
