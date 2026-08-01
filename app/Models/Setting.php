<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeGeneral($query)
    {
        return $query->where('group', 'general');
    }

    public function scopeInvoice($query)
    {
        return $query->where('group', 'invoice');
    }

    public function scopeTax($query)
    {
        return $query->where('group', 'tax');
    }
}