<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\BrandPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchasePolicy;
use App\Policies\SalePolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Brand::class => BrandPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Customer::class => CustomerPolicy::class,
        Product::class => ProductPolicy::class,
        Purchase::class => PurchasePolicy::class,
        Sale::class => SalePolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define a super admin gate
        Gate::before(function ($user, $ability) {
            if ($user->role_id === 1) {
                return true;
            }
        });
    }
}