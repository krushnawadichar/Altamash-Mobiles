<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'is_active',
        'role_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission($permission)
    {
        return $this->role->permissions->contains('name', $permission);
    }

    public function createdCategories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    public function createdBrands()
    {
        return $this->hasMany(Brand::class, 'created_by');
    }

    public function createdSuppliers()
    {
        return $this->hasMany(Supplier::class, 'created_by');
    }

    public function createdCustomers()
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function createdPurchases()
    {
        return $this->hasMany(Purchase::class, 'created_by');
    }

    public function createdSales()
    {
        return $this->hasMany(Sale::class, 'created_by');
    }

    public function createdRepairs()
    {
        return $this->hasMany(Repair::class, 'created_by');
    }

    public function createdExpenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }
}