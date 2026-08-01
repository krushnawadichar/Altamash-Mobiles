<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\AccessoryController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\RepairController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

        // Categories
        Route::resource('categories', CategoryController::class);

        // Brands
        Route::resource('brands', BrandController::class);

        // Suppliers
        Route::resource('suppliers', SupplierController::class);

        // Customers
        Route::resource('customers', CustomerController::class);

        // Products
        Route::resource('products', ProductController::class);
        Route::get('/products/{product}/barcode', [ProductController::class, 'generateBarcode'])->name('products.barcode');

        // Accessories
        Route::resource('accessories', AccessoryController::class);
        Route::get('/accessories/{accessory}/barcode', [AccessoryController::class, 'generateBarcode'])->name('accessories.barcode');

        // Purchases
        Route::resource('purchases', PurchaseController::class);
        Route::get('/purchases/get-items', [PurchaseController::class, 'getItems'])->name('purchases.get-items');
        Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
        Route::get('/purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])->name('purchases.pdf');

        // Sales
        Route::resource('sales', SaleController::class);
        Route::get('/sales/get-items', [SaleController::class, 'getItems'])->name('sales.get-items');
        Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
        Route::get('/sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/adjustment', [InventoryController::class, 'adjustment'])->name('inventory.adjustment');
        Route::post('/inventory/transfer', [InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::post('/inventory/damage', [InventoryController::class, 'damage'])->name('inventory.damage');
        Route::post('/inventory/lost', [InventoryController::class, 'lost'])->name('inventory.lost');
        Route::get('/inventory/get-items', [InventoryController::class, 'getItems'])->name('inventory.get-items');

        // Repairs
        Route::resource('repairs', RepairController::class);
        Route::get('/repairs/{repair}/print', [RepairController::class, 'print'])->name('repairs.print');
        Route::get('/repairs/{repair}/pdf', [RepairController::class, 'pdf'])->name('repairs.pdf');
        Route::post('/repairs/{repair}/status', [RepairController::class, 'updateStatus'])->name('repairs.status');

        // Expenses
        Route::resource('expenses', ExpenseController::class);

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/purchases', [ReportController::class, 'purchases'])->name('reports.purchases');
        Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
        Route::get('/reports/expenses', [ReportController::class, 'expenses'])->name('reports.expenses');
        Route::get('/reports/repairs', [ReportController::class, 'repairs'])->name('reports.repairs');
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('/reports/suppliers', [ReportController::class, 'suppliers'])->name('reports.suppliers');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Users
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/backup', [SettingController::class, 'backup'])->name('settings.backup');
        Route::post('/settings/restore', [SettingController::class, 'restore'])->name('settings.restore');
        Route::get('/settings/backup-list', [SettingController::class, 'getBackupFiles'])->name('settings.backup-list');
        Route::get('/settings/download-backup/{filename}', [SettingController::class, 'downloadBackup'])->name('settings.download-backup');
        Route::delete('/settings/delete-backup/{filename}', [SettingController::class, 'deleteBackup'])->name('settings.delete-backup');

        // Search
        Route::get('/search', [DashboardController::class, 'search'])->name('search');
    });
});

require __DIR__.'/auth.php';