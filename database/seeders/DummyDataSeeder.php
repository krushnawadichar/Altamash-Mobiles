<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Unit;
use App\Models\ProductType;
use App\Models\MobileCompany;
use App\Models\RepairStatus;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Accessory;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Inventory;
use App\Models\Repair;
use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. CREATE DEFAULT SETTINGS
        // ============================================
        $this->createSettings();

        // ============================================
        // 2. CREATE UNITS
        // ============================================
        $this->createUnits();

        // ============================================
        // 3. CREATE PRODUCT TYPES
        // ============================================
        $this->createProductTypes();

        // ============================================
        // 4. CREATE MOBILE COMPANIES
        // ============================================
        $this->createMobileCompanies();

        // ============================================
        // 5. CREATE REPAIR STATUSES
        // ============================================
        $this->createRepairStatuses();

        // ============================================
        // 6. CREATE EXPENSE CATEGORIES
        // ============================================
        $this->createExpenseCategories();

        // ============================================
        // 7. CREATE CATEGORIES
        // ============================================
        $this->createCategories();

        // ============================================
        // 8. CREATE BRANDS
        // ============================================
        $this->createBrands();

        // ============================================
        // 9. CREATE SUPPLIERS
        // ============================================
        $this->createSuppliers();

        // ============================================
        // 10. CREATE CUSTOMERS
        // ============================================
        $this->createCustomers();

        // ============================================
        // 11. CREATE PRODUCTS
        // ============================================
        $this->createProducts();

        // ============================================
        // 12. CREATE ACCESSORIES
        // ============================================
        $this->createAccessories();

        // ============================================
        // 13. CREATE PURCHASES
        // ============================================
        $this->createPurchases();

        // ============================================
        // 14. CREATE SALES
        // ============================================
        $this->createSales();

        // ============================================
        // 15. CREATE REPAIRS
        // ============================================
        $this->createRepairs();

        // ============================================
        // 16. CREATE EXPENSES
        // ============================================
        $this->createExpenses();

        $this->command->info('All dummy data created successfully!');
    }

    private function createSettings(): void
    {
        $settings = [
            ['key' => 'shop_name', 'value' => 'Altamash Mobiles', 'group' => 'general', 'type' => 'string'],
            ['key' => 'shop_logo', 'value' => null, 'group' => 'general', 'type' => 'string'],
            ['key' => 'shop_address', 'value' => 'Main Market, Gulberg, Lahore, Pakistan', 'group' => 'general', 'type' => 'text'],
            ['key' => 'shop_phone', 'value' => '+92-300-1234567', 'group' => 'general', 'type' => 'string'],
            ['key' => 'shop_email', 'value' => 'info@altamashmobiles.com', 'group' => 'general', 'type' => 'string'],
            ['key' => 'gst_number', 'value' => '1234567890123', 'group' => 'general', 'type' => 'string'],
            ['key' => 'currency', 'value' => 'PKR', 'group' => 'general', 'type' => 'string'],
            ['key' => 'timezone', 'value' => 'Asia/Karachi', 'group' => 'general', 'type' => 'string'],
            ['key' => 'invoice_prefix', 'value' => 'INV-', 'group' => 'invoice', 'type' => 'string'],
            ['key' => 'purchase_prefix', 'value' => 'PO-', 'group' => 'invoice', 'type' => 'string'],
            ['key' => 'repair_prefix', 'value' => 'REP-', 'group' => 'invoice', 'type' => 'string'],
            ['key' => 'default_gst', 'value' => '18', 'group' => 'tax', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function createUnits(): void
    {
        $units = [
            ['name' => 'Piece', 'code' => 'PCS', 'symbol' => 'pc'],
            ['name' => 'Box', 'code' => 'BOX', 'symbol' => 'box'],
            ['name' => 'Set', 'code' => 'SET', 'symbol' => 'set'],
            ['name' => 'Pair', 'code' => 'PR', 'symbol' => 'pr'],
            ['name' => 'Dozen', 'code' => 'DZN', 'symbol' => 'dzn'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(
                ['code' => $unit['code']],
                [
                    'name' => $unit['name'],
                    'symbol' => $unit['symbol'],
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createProductTypes(): void
    {
        $types = [
            'New Phone',
            'Used Phone',
            'Refurbished',
            'Accessory',
            'Spare Part',
        ];

        foreach ($types as $type) {
            ProductType::firstOrCreate(
                ['slug' => Str::slug($type)],
                [
                    'name' => $type,
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createMobileCompanies(): void
    {
        $companies = [
            ['name' => 'Samsung', 'country' => 'South Korea'],
            ['name' => 'Apple', 'country' => 'USA'],
            ['name' => 'OnePlus', 'country' => 'China'],
            ['name' => 'Xiaomi', 'country' => 'China'],
            ['name' => 'Oppo', 'country' => 'China'],
            ['name' => 'Vivo', 'country' => 'China'],
            ['name' => 'Google', 'country' => 'USA'],
            ['name' => 'Nokia', 'country' => 'Finland'],
            ['name' => 'Sony', 'country' => 'Japan'],
            ['name' => 'Huawei', 'country' => 'China'],
        ];

        foreach ($companies as $company) {
            MobileCompany::firstOrCreate(
                ['name' => $company['name']],
                [
                    'country' => $company['country'],
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createRepairStatuses(): void
    {
        $statuses = [
            ['name' => 'Pending', 'color' => '#ffc107'],
            ['name' => 'Checking', 'color' => '#17a2b8'],
            ['name' => 'Waiting Parts', 'color' => '#fd7e14'],
            ['name' => 'Repairing', 'color' => '#6f42c1'],
            ['name' => 'Completed', 'color' => '#28a745'],
            ['name' => 'Delivered', 'color' => '#007bff'],
            ['name' => 'Cancelled', 'color' => '#dc3545'],
        ];

        foreach ($statuses as $status) {
            RepairStatus::firstOrCreate(
                ['name' => $status['name']],
                [
                    'color' => $status['color'],
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createExpenseCategories(): void
    {
        $categories = [
            'Shop Rent',
            'Electricity',
            'Internet',
            'Salary',
            'Tea',
            'Petrol',
            'Maintenance',
            'Marketing',
            'Other Expenses',
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(
                ['slug' => Str::slug($category)],
                [
                    'name' => $category,
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createCategories(): void
    {
        $categories = [
            'Mobile Phones' => ['icon' => 'fas fa-mobile-alt', 'description' => 'Latest smartphones from top brands'],
            'Accessories' => ['icon' => 'fas fa-headphones', 'description' => 'Mobile accessories including covers, chargers, and more'],
            'Tablets' => ['icon' => 'fas fa-tablet-alt', 'description' => 'Tablets from leading manufacturers'],
            'Smart Watches' => ['icon' => 'fas fa-clock', 'description' => 'Smart watches and fitness trackers'],
            'Laptops' => ['icon' => 'fas fa-laptop', 'description' => 'Laptops and notebooks'],
            'Earphones' => ['icon' => 'fas fa-headphones', 'description' => 'Wired and wireless earphones'],
            'Speakers' => ['icon' => 'fas fa-volume-up', 'description' => 'Bluetooth and wired speakers'],
            'Power Banks' => ['icon' => 'fas fa-battery-full', 'description' => 'Portable power banks'],
            'Chargers' => ['icon' => 'fas fa-plug', 'description' => 'Wall chargers and car chargers'],
            'Cables' => ['icon' => 'fas fa-plug', 'description' => 'USB cables and data cables'],
        ];

        foreach ($categories as $name => $data) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'icon' => $data['icon'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createBrands(): void
    {
        $brands = [
            'Samsung' => ['logo' => null, 'description' => 'South Korean multinational electronics company'],
            'Apple' => ['logo' => null, 'description' => 'American technology company'],
            'OnePlus' => ['logo' => null, 'description' => 'Chinese smartphone manufacturer'],
            'Xiaomi' => ['logo' => null, 'description' => 'Chinese electronics company'],
            'Oppo' => ['logo' => null, 'description' => 'Chinese consumer electronics manufacturer'],
            'Vivo' => ['logo' => null, 'description' => 'Chinese smartphone manufacturer'],
            'Realme' => ['logo' => null, 'description' => 'Chinese smartphone brand'],
            'Tecno' => ['logo' => null, 'description' => 'Chinese mobile phone manufacturer'],
            'Infinix' => ['logo' => null, 'description' => 'Hong Kong-based smartphone brand'],
            'Nokia' => ['logo' => null, 'description' => 'Finnish telecommunications company'],
            'Google' => ['logo' => null, 'description' => 'American technology company'],
            'Sony' => ['logo' => null, 'description' => 'Japanese electronics company'],
            'Huawei' => ['logo' => null, 'description' => 'Chinese technology company'],
            'Lenovo' => ['logo' => null, 'description' => 'Chinese technology company'],
            'Dell' => ['logo' => null, 'description' => 'American technology company'],
        ];

        foreach ($brands as $name => $data) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'logo' => $data['logo'],
                    'description' => $data['description'],
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }
    }

    private function createSuppliers(): void
    {
        $suppliers = [
            [
                'name' => 'Tech Distributors Ltd',
                'email' => 'info@techdistributors.com',
                'phone' => '+92-300-1111111',
                'alternative_phone' => '+92-300-2222222',
                'address' => '123 Main Street, Lahore, Pakistan',
                'company_name' => 'Tech Distributors Ltd',
                'gst_number' => 'GST-001-ABCD',
                'opening_balance' => 0,
                'notes' => 'Main supplier for mobile phones',
            ],
            [
                'name' => 'Mobile World Suppliers',
                'email' => 'info@mobileworld.com',
                'phone' => '+92-300-3333333',
                'alternative_phone' => '+92-300-4444444',
                'address' => '456 Model Town, Lahore, Pakistan',
                'company_name' => 'Mobile World Suppliers',
                'gst_number' => 'GST-002-EFGH',
                'opening_balance' => 0,
                'notes' => 'Supplier for accessories',
            ],
            [
                'name' => 'Digital Solutions Inc',
                'email' => 'info@digitalsolutions.com',
                'phone' => '+92-300-5555555',
                'alternative_phone' => '+92-300-6666666',
                'address' => '789 Gulberg, Lahore, Pakistan',
                'company_name' => 'Digital Solutions Inc',
                'gst_number' => 'GST-003-IJKL',
                'opening_balance' => 0,
                'notes' => 'Supplier for tablets and laptops',
            ],
            [
                'name' => 'Global Electronics',
                'email' => 'info@globalelectronics.com',
                'phone' => '+92-300-7777777',
                'alternative_phone' => '+92-300-8888888',
                'address' => '321 Johar Town, Lahore, Pakistan',
                'company_name' => 'Global Electronics',
                'gst_number' => 'GST-004-MNOP',
                'opening_balance' => 0,
                'notes' => 'International electronics supplier',
            ],
            [
                'name' => 'Smart Mobile Traders',
                'email' => 'info@smartmobile.com',
                'phone' => '+92-300-9999999',
                'alternative_phone' => '+92-300-0000000',
                'address' => '654 DHA, Lahore, Pakistan',
                'company_name' => 'Smart Mobile Traders',
                'gst_number' => 'GST-005-QRST',
                'opening_balance' => 0,
                'notes' => 'Used and refurbished phones',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create([
                'name' => $supplier['name'],
                'email' => $supplier['email'],
                'phone' => $supplier['phone'],
                'alternative_phone' => $supplier['alternative_phone'],
                'address' => $supplier['address'],
                'company_name' => $supplier['company_name'],
                'gst_number' => $supplier['gst_number'],
                'opening_balance' => $supplier['opening_balance'],
                'current_balance' => 0,
                'notes' => $supplier['notes'],
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }

    private function createCustomers(): void
    {
        $customers = [
            [
                'name' => 'Ahmed Khan',
                'email' => 'ahmed@example.com',
                'phone' => '+92-300-1112222',
                'alternative_phone' => '+92-300-3334444',
                'address' => '123 Model Town, Lahore',
                'gst_number' => 'GST-CUST-001',
            ],
            [
                'name' => 'Sana Ali',
                'email' => 'sana@example.com',
                'phone' => '+92-300-5556666',
                'alternative_phone' => '+92-300-7778888',
                'address' => '456 Gulberg, Lahore',
                'gst_number' => 'GST-CUST-002',
            ],
            [
                'name' => 'Usman Raza',
                'email' => 'usman@example.com',
                'phone' => '+92-300-9990000',
                'alternative_phone' => '+92-300-1112223',
                'address' => '789 Johar Town, Lahore',
                'gst_number' => 'GST-CUST-003',
            ],
            [
                'name' => 'Fatima Noor',
                'email' => 'fatima@example.com',
                'phone' => '+92-300-4445555',
                'alternative_phone' => '+92-300-6667777',
                'address' => '321 DHA, Lahore',
                'gst_number' => 'GST-CUST-004',
            ],
            [
                'name' => 'Bilal Ahmed',
                'email' => 'bilal@example.com',
                'phone' => '+92-300-8889999',
                'alternative_phone' => '+92-300-0001111',
                'address' => '654 Walton Road, Lahore',
                'gst_number' => 'GST-CUST-005',
            ],
            [
                'name' => 'Ayesha Hassan',
                'email' => 'ayesha@example.com',
                'phone' => '+92-300-2223333',
                'alternative_phone' => '+92-300-4445556',
                'address' => '987 Faisal Town, Lahore',
                'gst_number' => 'GST-CUST-006',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create([
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'alternative_phone' => $customer['alternative_phone'],
                'address' => $customer['address'],
                'gst_number' => $customer['gst_number'],
                'opening_balance' => 0,
                'current_balance' => 0,
                'total_purchases' => 0,
                'total_purchase_amount' => 0,
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }

    private function createProducts(): void
    {
        $products = [
            // Samsung Phones
            [
                'name' => 'Samsung Galaxy S24 Ultra 5G',
                'category' => 'Mobile Phones',
                'brand' => 'Samsung',
                'supplier' => 'Tech Distributors Ltd',
                'mobile_company' => 'Samsung',
                'purchase_price' => 120000,
                'selling_price' => 150000,
                'color' => 'Phantom Black',
                'storage' => '256GB',
                'ram' => '12GB',
                'minimum_stock' => 5,
                'current_stock' => 15,
            ],
            [
                'name' => 'Samsung Galaxy Z Fold 5',
                'category' => 'Mobile Phones',
                'brand' => 'Samsung',
                'supplier' => 'Tech Distributors Ltd',
                'mobile_company' => 'Samsung',
                'purchase_price' => 180000,
                'selling_price' => 220000,
                'color' => 'Phantom Black',
                'storage' => '512GB',
                'ram' => '12GB',
                'minimum_stock' => 3,
                'current_stock' => 8,
            ],
            [
                'name' => 'Samsung Galaxy A55 5G',
                'category' => 'Mobile Phones',
                'brand' => 'Samsung',
                'supplier' => 'Tech Distributors Ltd',
                'mobile_company' => 'Samsung',
                'purchase_price' => 70000,
                'selling_price' => 85000,
                'color' => 'Ice Blue',
                'storage' => '128GB',
                'ram' => '8GB',
                'minimum_stock' => 10,
                'current_stock' => 20,
            ],
            // Apple Phones
            [
                'name' => 'iPhone 15 Pro Max',
                'category' => 'Mobile Phones',
                'brand' => 'Apple',
                'supplier' => 'Global Electronics',
                'mobile_company' => 'Apple',
                'purchase_price' => 180000,
                'selling_price' => 220000,
                'color' => 'Natural Titanium',
                'storage' => '256GB',
                'ram' => '8GB',
                'minimum_stock' => 5,
                'current_stock' => 12,
            ],
            [
                'name' => 'iPhone 15 Pro',
                'category' => 'Mobile Phones',
                'brand' => 'Apple',
                'supplier' => 'Global Electronics',
                'mobile_company' => 'Apple',
                'purchase_price' => 150000,
                'selling_price' => 180000,
                'color' => 'Blue Titanium',
                'storage' => '128GB',
                'ram' => '8GB',
                'minimum_stock' => 8,
                'current_stock' => 10,
            ],
            [
                'name' => 'iPhone 15 Plus',
                'category' => 'Mobile Phones',
                'brand' => 'Apple',
                'supplier' => 'Global Electronics',
                'mobile_company' => 'Apple',
                'purchase_price' => 120000,
                'selling_price' => 150000,
                'color' => 'Pink',
                'storage' => '128GB',
                'ram' => '6GB',
                'minimum_stock' => 8,
                'current_stock' => 15,
            ],
            // OnePlus Phones
            [
                'name' => 'OnePlus 12',
                'category' => 'Mobile Phones',
                'brand' => 'OnePlus',
                'supplier' => 'Digital Solutions Inc',
                'mobile_company' => 'OnePlus',
                'purchase_price' => 100000,
                'selling_price' => 120000,
                'color' => 'Silky Black',
                'storage' => '256GB',
                'ram' => '12GB',
                'minimum_stock' => 5,
                'current_stock' => 10,
            ],
            [
                'name' => 'OnePlus 12R',
                'category' => 'Mobile Phones',
                'brand' => 'OnePlus',
                'supplier' => 'Digital Solutions Inc',
                'mobile_company' => 'OnePlus',
                'purchase_price' => 70000,
                'selling_price' => 90000,
                'color' => 'Cool Blue',
                'storage' => '128GB',
                'ram' => '8GB',
                'minimum_stock' => 8,
                'current_stock' => 18,
            ],
            // Xiaomi Phones
            [
                'name' => 'Xiaomi 14 Pro',
                'category' => 'Mobile Phones',
                'brand' => 'Xiaomi',
                'supplier' => 'Mobile World Suppliers',
                'mobile_company' => 'Xiaomi',
                'purchase_price' => 90000,
                'selling_price' => 110000,
                'color' => 'Black',
                'storage' => '256GB',
                'ram' => '12GB',
                'minimum_stock' => 5,
                'current_stock' => 12,
            ],
            [
                'name' => 'Xiaomi 14',
                'category' => 'Mobile Phones',
                'brand' => 'Xiaomi',
                'supplier' => 'Mobile World Suppliers',
                'mobile_company' => 'Xiaomi',
                'purchase_price' => 70000,
                'selling_price' => 85000,
                'color' => 'White',
                'storage' => '128GB',
                'ram' => '8GB',
                'minimum_stock' => 8,
                'current_stock' => 20,
            ],
            // Oppo Phones
            [
                'name' => 'Oppo Find X7 Ultra',
                'category' => 'Mobile Phones',
                'brand' => 'Oppo',
                'supplier' => 'Mobile World Suppliers',
                'mobile_company' => 'Oppo',
                'purchase_price' => 110000,
                'selling_price' => 140000,
                'color' => 'Ocean Blue',
                'storage' => '256GB',
                'ram' => '12GB',
                'minimum_stock' => 3,
                'current_stock' => 8,
            ],
            [
                'name' => 'Oppo Reno 11 Pro',
                'category' => 'Mobile Phones',
                'brand' => 'Oppo',
                'supplier' => 'Mobile World Suppliers',
                'mobile_company' => 'Oppo',
                'purchase_price' => 80000,
                'selling_price' => 100000,
                'color' => 'Pearl White',
                'storage' => '256GB',
                'ram' => '8GB',
                'minimum_stock' => 5,
                'current_stock' => 15,
            ],
            // Vivo Phones
            [
                'name' => 'Vivo X100 Pro',
                'category' => 'Mobile Phones',
                'brand' => 'Vivo',
                'supplier' => 'Smart Mobile Traders',
                'mobile_company' => 'Vivo',
                'purchase_price' => 100000,
                'selling_price' => 125000,
                'color' => 'Astral Blue',
                'storage' => '256GB',
                'ram' => '12GB',
                'minimum_stock' => 3,
                'current_stock' => 6,
            ],
            [
                'name' => 'Vivo V30 Pro',
                'category' => 'Mobile Phones',
                'brand' => 'Vivo',
                'supplier' => 'Smart Mobile Traders',
                'mobile_company' => 'Vivo',
                'purchase_price' => 60000,
                'selling_price' => 75000,
                'color' => 'Black',
                'storage' => '128GB',
                'ram' => '8GB',
                'minimum_stock' => 8,
                'current_stock' => 18,
            ],
        ];

        $categoryIds = Category::pluck('id', 'name')->toArray();
        $brandIds = Brand::pluck('id', 'name')->toArray();
        $supplierIds = Supplier::pluck('id', 'name')->toArray();
        $mobileCompanyIds = MobileCompany::pluck('id', 'name')->toArray();
        $unitIds = Unit::pluck('id', 'code')->toArray();
        $productTypeIds = ProductType::pluck('id', 'name')->toArray();

        foreach ($products as $product) {
            $sku = 'PRD-' . strtoupper(Str::random(8));
            
            Product::create([
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'sku' => $sku,
                'barcode' => 'BC' . rand(10000000, 99999999),
                'category_id' => $categoryIds[$product['category']] ?? 1,
                'brand_id' => $brandIds[$product['brand']] ?? 1,
                'supplier_id' => $supplierIds[$product['supplier']] ?? null,
                'unit_id' => $unitIds['PCS'] ?? 1,
                'product_type_id' => $productTypeIds['New Phone'] ?? 1,
                'mobile_company_id' => $mobileCompanyIds[$product['mobile_company']] ?? null,
                'purchase_price' => $product['purchase_price'],
                'selling_price' => $product['selling_price'],
                'gst_percentage' => 18,
                'tax_amount' => $product['selling_price'] * 0.18,
                'color' => $product['color'],
                'storage' => $product['storage'],
                'ram' => $product['ram'],
                'description' => "Latest model with advanced features",
                'minimum_stock' => $product['minimum_stock'],
                'current_stock' => $product['current_stock'],
                'status' => 'active',
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }

    private function createAccessories(): void
    {
        $accessories = [
            // Covers
            ['name' => 'Samsung S24 Ultra Silicone Cover', 'type' => 'Cover', 'purchase_price' => 500, 'selling_price' => 800, 'stock' => 50],
            ['name' => 'iPhone 15 Pro Max Leather Cover', 'type' => 'Cover', 'purchase_price' => 800, 'selling_price' => 1200, 'stock' => 30],
            ['name' => 'OnePlus 12 Carbon Cover', 'type' => 'Cover', 'purchase_price' => 400, 'selling_price' => 700, 'stock' => 40],
            
            // Tempered Glass
            ['name' => 'Samsung S24 Ultra Tempered Glass', 'type' => 'Tempered Glass', 'purchase_price' => 200, 'selling_price' => 400, 'stock' => 100],
            ['name' => 'iPhone 15 Pro Max Screen Protector', 'type' => 'Tempered Glass', 'purchase_price' => 250, 'selling_price' => 450, 'stock' => 80],
            ['name' => 'Xiaomi 14 Pro Glass Protector', 'type' => 'Tempered Glass', 'purchase_price' => 150, 'selling_price' => 300, 'stock' => 60],
            
            // Chargers
            ['name' => 'Samsung 45W Super Fast Charger', 'type' => 'Charger', 'purchase_price' => 1500, 'selling_price' => 2500, 'stock' => 25],
            ['name' => 'Apple 20W USB-C Charger', 'type' => 'Charger', 'purchase_price' => 2000, 'selling_price' => 3500, 'stock' => 20],
            ['name' => 'OnePlus 100W SuperVOOC Charger', 'type' => 'Charger', 'purchase_price' => 2500, 'selling_price' => 4000, 'stock' => 15],
            
            // Earphones
            ['name' => 'Samsung Galaxy Buds 2 Pro', 'type' => 'Earphones', 'purchase_price' => 8000, 'selling_price' => 12000, 'stock' => 10],
            ['name' => 'Apple AirPods Pro 2', 'type' => 'Earphones', 'purchase_price' => 15000, 'selling_price' => 20000, 'stock' => 8],
            ['name' => 'OnePlus Buds Pro 2', 'type' => 'Earphones', 'purchase_price' => 6000, 'selling_price' => 9000, 'stock' => 12],
            
            // Cables
            ['name' => 'Samsung USB-C Data Cable', 'type' => 'Cable', 'purchase_price' => 300, 'selling_price' => 500, 'stock' => 80],
            ['name' => 'Apple Lightning Cable', 'type' => 'Cable', 'purchase_price' => 500, 'selling_price' => 800, 'stock' => 60],
            ['name' => 'USB-C to USB-C Braided Cable', 'type' => 'Cable', 'purchase_price' => 200, 'selling_price' => 400, 'stock' => 100],
            
            // Power Banks
            ['name' => 'Samsung 10000mAh Power Bank', 'type' => 'Power Bank', 'purchase_price' => 3000, 'selling_price' => 4500, 'stock' => 15],
            ['name' => 'Xiaomi 20000mAh Power Bank', 'type' => 'Power Bank', 'purchase_price' => 4000, 'selling_price' => 6000, 'stock' => 12],
            
            // Bluetooth
            ['name' => 'Samsung Bluetooth Speaker', 'type' => 'Bluetooth', 'purchase_price' => 5000, 'selling_price' => 7500, 'stock' => 8],
            ['name' => 'JBL Bluetooth Speaker', 'type' => 'Bluetooth', 'purchase_price' => 6000, 'selling_price' => 8500, 'stock' => 6],
            
            // Memory Cards
            ['name' => 'Samsung 128GB MicroSD Card', 'type' => 'Memory Card', 'purchase_price' => 1500, 'selling_price' => 2500, 'stock' => 40],
            ['name' => 'Samsung 256GB MicroSD Card', 'type' => 'Memory Card', 'purchase_price' => 3000, 'selling_price' => 4500, 'stock' => 25],
            ['name' => 'SanDisk 512GB MicroSD Card', 'type' => 'Memory Card', 'purchase_price' => 5000, 'selling_price' => 7500, 'stock' => 15],
        ];

        $supplierIds = Supplier::pluck('id', 'name')->toArray();

        foreach ($accessories as $accessory) {
            $sku = 'ACC-' . strtoupper(Str::random(8));
            
            Accessory::create([
                'name' => $accessory['name'],
                'type' => $accessory['type'],
                'sku' => $sku,
                'barcode' => 'BC' . rand(10000000, 99999999),
                'supplier_id' => $supplierIds['Mobile World Suppliers'] ?? null,
                'purchase_price' => $accessory['purchase_price'],
                'selling_price' => $accessory['selling_price'],
                'gst_percentage' => 18,
                'current_stock' => $accessory['stock'],
                'minimum_stock' => 5,
                'description' => 'High quality ' . $accessory['type'] . ' for your device',
                'is_active' => true,
                'created_by' => 1,
            ]);
        }
    }

    private function createPurchases(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $accessories = Accessory::all();

        for ($i = 1; $i <= 15; $i++) {
            $supplier = $suppliers->random();
            $purchaseDate = Carbon::now()->subDays(rand(1, 60));
            
            // Select random items (3-6 items per purchase)
            $items = [];
            $totalItems = rand(3, 6);
            
            for ($j = 0; $j < $totalItems; $j++) {
                $isProduct = rand(0, 1);
                if ($isProduct && $products->count() > 0) {
                    $product = $products->random();
                    $quantity = rand(2, 10);
                    $items[] = [
                        'type' => 'product',
                        'id' => $product->id,
                        'quantity' => $quantity,
                        'purchase_price' => $product->purchase_price,
                        'selling_price' => $product->selling_price,
                        'name' => $product->name,
                    ];
                } elseif ($accessories->count() > 0) {
                    $accessory = $accessories->random();
                    $quantity = rand(5, 20);
                    $items[] = [
                        'type' => 'accessory',
                        'id' => $accessory->id,
                        'quantity' => $quantity,
                        'purchase_price' => $accessory->purchase_price,
                        'selling_price' => $accessory->selling_price,
                        'name' => $accessory->name,
                    ];
                }
            }

            if (empty($items)) continue;

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['purchase_price'] * $item['quantity'];
            }
            
            $discount = rand(0, 5) / 100 * $subtotal;
            $gstAmount = ($subtotal - $discount) * 0.18;
            $totalAmount = $subtotal - $discount + $gstAmount;
            $paidAmount = rand(0, 1) ? $totalAmount : rand(0, intval($totalAmount));
            $dueAmount = $totalAmount - $paidAmount;

            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending');

            // Create Purchase
            $purchase = Purchase::create([
                'invoice_number' => 'PO-' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'purchase_date' => $purchaseDate,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'gst_amount' => $gstAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'notes' => 'Purchase order #' . $i,
                'status' => 'completed',
                'created_by' => 1,
            ]);

            // Create Purchase Details
            foreach ($items as $item) {
                $detail = PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'purchasable_id' => $item['id'],
                    'purchasable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
                    'quantity' => $item['quantity'],
                    'purchase_price' => $item['purchase_price'],
                    'selling_price' => $item['selling_price'],
                    'total' => $item['purchase_price'] * $item['quantity'],
                ]);

                // Update inventory
                Inventory::create([
                    'inventoriable_id' => $item['id'],
                    'inventoriable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
                    'type' => 'purchase',
                    'quantity' => $item['quantity'],
                    'price' => $item['purchase_price'],
                    'total_price' => $item['purchase_price'] * $item['quantity'],
                    'reference_id' => $purchase->id,
                    'reference_type' => 'App\Models\Purchase',
                    'remarks' => 'Purchase from ' . $supplier->name,
                    'created_by' => 1,
                ]);

                // Update stock
                if ($item['type'] === 'product') {
                    $product = Product::find($item['id']);
                    if ($product) {
                        $product->current_stock += $item['quantity'];
                        $product->save();
                    }
                } else {
                    $accessory = Accessory::find($item['id']);
                    if ($accessory) {
                        $accessory->current_stock += $item['quantity'];
                        $accessory->save();
                    }
                }
            }

            // Update supplier balance
            $supplier->current_balance += $dueAmount;
            $supplier->save();
        }
    }

    private function createSales(): void
    {
        $customers = Customer::all();
        $products = Product::all();
        $accessories = Accessory::all();

        for ($i = 1; $i <= 25; $i++) {
            $customer = $customers->random();
            $saleDate = Carbon::now()->subDays(rand(0, 30));
            
            // Select random items (2-5 items per sale)
            $items = [];
            $totalItems = rand(2, 5);
            
            for ($j = 0; $j < $totalItems; $j++) {
                $isProduct = rand(0, 1);
                if ($isProduct && $products->count() > 0) {
                    $product = $products->random();
                    if ($product->current_stock <= 0) continue;
                    $quantity = rand(1, min(3, $product->current_stock));
                    if ($quantity <= 0) continue;
                    $items[] = [
                        'type' => 'product',
                        'id' => $product->id,
                        'quantity' => $quantity,
                        'selling_price' => $product->selling_price,
                        'purchase_price' => $product->purchase_price,
                        'name' => $product->name,
                    ];
                    $product->current_stock -= $quantity;
                    $product->save();
                } elseif ($accessories->count() > 0) {
                    $accessory = $accessories->random();
                    if ($accessory->current_stock <= 0) continue;
                    $quantity = rand(1, min(5, $accessory->current_stock));
                    if ($quantity <= 0) continue;
                    $items[] = [
                        'type' => 'accessory',
                        'id' => $accessory->id,
                        'quantity' => $quantity,
                        'selling_price' => $accessory->selling_price,
                        'purchase_price' => $accessory->purchase_price,
                        'name' => $accessory->name,
                    ];
                    $accessory->current_stock -= $quantity;
                    $accessory->save();
                }
            }

            if (empty($items)) continue;

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['selling_price'] * $item['quantity'];
            }
            
            $discount = rand(0, 10) / 100 * $subtotal;
            $gstAmount = ($subtotal - $discount) * 0.18;
            $totalAmount = $subtotal - $discount + $gstAmount;
            $paidAmount = rand(0, 1) ? $totalAmount : rand(0, intval($totalAmount));
            $dueAmount = $totalAmount - $paidAmount;

            $paymentStatus = $dueAmount <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending');

            // Create Sale
            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'sale_date' => $saleDate,
                'payment_method' => ['cash', 'card', 'online'][rand(0, 2)],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'gst_amount' => $gstAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $paymentStatus,
                'notes' => 'Sale #' . $i,
                'status' => 'completed',
                'created_by' => 1,
            ]);

            // Create Sale Details
            foreach ($items as $item) {
                $profit = ($item['selling_price'] - $item['purchase_price']) * $item['quantity'];
                
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'sellable_id' => $item['id'],
                    'sellable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
                    'quantity' => $item['quantity'],
                    'selling_price' => $item['selling_price'],
                    'purchase_price' => $item['purchase_price'],
                    'total' => $item['selling_price'] * $item['quantity'],
                    'profit' => $profit,
                ]);

                // Update inventory
                Inventory::create([
                    'inventoriable_id' => $item['id'],
                    'inventoriable_type' => $item['type'] === 'product' ? 'App\Models\Product' : 'App\Models\Accessory',
                    'type' => 'sale',
                    'quantity' => -$item['quantity'],
                    'price' => $item['selling_price'],
                    'total_price' => -($item['selling_price'] * $item['quantity']),
                    'reference_id' => $sale->id,
                    'reference_type' => 'App\Models\Sale',
                    'remarks' => 'Sale to ' . $customer->name,
                    'created_by' => 1,
                ]);
            }

            // Update customer balance
            if ($dueAmount > 0) {
                $customer->current_balance += $dueAmount;
                $customer->total_purchases += 1;
                $customer->total_purchase_amount += $totalAmount;
                $customer->save();
            }
        }
    }

    private function createRepairs(): void
    {
        $customers = Customer::all();
        $repairStatuses = RepairStatus::all();

        for ($i = 1; $i <= 15; $i++) {
            $customer = $customers->random();
            $status = $repairStatuses->random();
            $receiveDate = Carbon::now()->subDays(rand(1, 20));
            
            $estimatedCost = rand(2000, 30000);
            $advancePaid = rand(0, 1) ? rand(0, intval($estimatedCost * 0.5)) : 0;
            $remaining = $estimatedCost - $advancePaid;

            $repair = Repair::create([
                'repair_number' => 'REP-' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_mobile' => $customer->phone,
                'device_name' => ['Samsung Galaxy S24 Ultra', 'iPhone 15 Pro Max', 'OnePlus 12', 'Xiaomi 14 Pro', 'Oppo Find X7 Ultra'][rand(0, 4)],
                'imei' => rand(100000000000000, 999999999999999),
                'issue' => ['Screen cracked', 'Battery not charging', 'Water damage', 'Camera not working', 'Software issue', 'Speaker not working'][rand(0, 5)],
                'accessories_received' => 'Charger, Box, Earphones',
                'estimated_cost' => $estimatedCost,
                'advance_paid' => $advancePaid,
                'remaining_amount' => $remaining,
                'engineer_notes' => 'Need to check thoroughly',
                'repair_status_id' => $status->id,
                'receive_date' => $receiveDate,
                'delivery_date' => $status->name == 'Completed' ? $receiveDate->addDays(rand(2, 7)) : null,
                'payment_status' => $remaining <= 0 ? 'paid' : ($advancePaid > 0 ? 'partial' : 'pending'),
                'created_by' => 1,
            ]);
        }
    }

    private function createExpenses(): void
    {
        $expenseCategories = ExpenseCategory::all();

        for ($i = 1; $i <= 30; $i++) {
            $category = $expenseCategories->random();
            $expenseDate = Carbon::now()->subDays(rand(0, 30));
            
            Expense::create([
                'expense_category_id' => $category->id,
                'title' => $category->name . ' - ' . $i,
                'amount' => rand(500, 50000),
                'expense_date' => $expenseDate,
                'description' => 'Monthly ' . strtolower($category->name) . ' expense',
                'payment_method' => ['cash', 'card', 'bank'][rand(0, 2)],
                'status' => ['paid', 'paid', 'paid', 'pending'][rand(0, 3)],
                'created_by' => 1,
            ]);
        }
    }
}