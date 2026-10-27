<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'city', 'state',
        'total_purchases', 'status'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getTotalDueAttribute()
    {
        return (float)$this->sales()->sum('due_amount');
    }
}
