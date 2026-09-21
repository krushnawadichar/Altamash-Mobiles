<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('admin.dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile & Password
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'changePassword'])->name('profile.password');

    // Products & Barcodes
    Route::get('/products/{product}/barcode', [ProductController::class, 'barcode'])->name('products.barcode');
    Route::resource('products', ProductController::class);

    // Categories & Brands
    Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('brands', BrandController::class)->except(['create', 'edit', 'show']);

    // Inventory Management & IMEI Tracking
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/imeis', [InventoryController::class, 'imeis'])->name('inventory.imeis');
    Route::get('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');
    Route::resource('inventory/adjustments', StockAdjustmentController::class)->names('inventory.adjustments')->only(['index', 'create', 'store']);

    // Suppliers & Purchases
    Route::resource('suppliers', SupplierController::class);
    Route::post('/suppliers/{supplier}/payment', [SupplierController::class, 'makePayment'])->name('suppliers.payment');
    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/payment', [PurchaseController::class, 'addPayment'])->name('purchases.payment');

    // POS & Sales Management
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos', [PosController::class, 'store']);
    Route::post('/pos/sale', [PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/invoice/{sale}', [PosController::class, 'invoice'])->name('pos.invoice');
    Route::get('/pos/invoice/{sale}/pdf', [PosController::class, 'downloadPdf'])->name('pos.pdf');

    Route::resource('sales', SaleController::class)->only(['index', 'show']);
    Route::post('/sales/{sale}/payment', [SaleController::class, 'addPayment'])->name('sales.payment');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

    // Sales Returns
    Route::resource('sales-returns', SaleReturnController::class)->names('sales.returns')->only(['index', 'create', 'store']);

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::post('/customers/{customer}/payment', [CustomerController::class, 'collectPayment'])->name('customers.payment');

    // Mobile Repairs & Technicians
    Route::resource('repairs', RepairController::class);
    Route::post('/repairs/{repair}/add-part', [RepairController::class, 'addPart'])->name('repairs.add-part');
    Route::post('/repairs/{repair}/update-status', [RepairController::class, 'updateStatus'])->name('repairs.update-status');
    Route::post('/repairs/{repair}/payment', [RepairController::class, 'addPayment'])->name('repairs.payment');
    Route::get('/repairs/{repair}/jobcard', [RepairController::class, 'jobCard'])->name('repairs.jobcard');
    Route::get('/repairs/{repair}/jobcard/pdf', [RepairController::class, 'pdfJobCard'])->name('repairs.jobcard.pdf');
    Route::get('/repairs/{repair}/invoice', [RepairController::class, 'invoice'])->name('repairs.invoice');
    Route::get('/repairs/{repair}/invoice/pdf', [RepairController::class, 'pdfInvoice'])->name('repairs.invoice.pdf');

    Route::resource('technicians', TechnicianController::class);

    // Expenses
    Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'destroy']);
    Route::post('/expenses/categories', [ExpenseController::class, 'storeCategory'])->name('expenses.categories.store');

    // Comprehensive Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/mobiles', [ReportController::class, 'mobiles'])->name('mobiles');
        Route::get('/repairs', [ReportController::class, 'repairs'])->name('repairs');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
    });

    // Admin-only: Settings, Users, Activity Logs
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});
