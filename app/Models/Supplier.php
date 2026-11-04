<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'company_name', 'phone', 'email', 'address',
        'gst_number', 'city', 'state', 'notes', 'status'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getTotalPurchasesAttribute()
    {
        return (float)$this->purchases()->sum('grand_total');
    }

    public function getTotalDueAttribute()
    {
        return (float)$this->purchases()->sum('due_amount');
    }
}
