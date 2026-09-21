<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\InventoryTransaction;
use App\Models\Permission;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\RepairJob;
use App\Models\RepairPartUsed;
use App\Models\RepairPayment;
use App\Models\RepairStatusLog;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions
        $superAdminRole = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Full access to system']);
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrative management']);
        $salesmanRole = Role::create(['name' => 'Salesman', 'slug' => 'salesman', 'description' => 'POS, Sales & Customer management']);
        $techRole = Role::create(['name' => 'Technician', 'slug' => 'technician', 'description' => 'Repair jobs and assigned tasks']);

        $permissions = [
            ['name' => 'View Products', 'slug' => 'view_products', 'group' => 'products'],
            ['name' => 'Create Product', 'slug' => 'create_product', 'group' => 'products'],
            ['name' => 'Edit Product', 'slug' => 'edit_product', 'group' => 'products'],
            ['name' => 'Delete Product', 'slug' => 'delete_product', 'group' => 'products'],
            ['name' => 'View Purchases', 'slug' => 'view_purchases', 'group' => 'purchases'],
            ['name' => 'Create Purchase', 'slug' => 'create_purchase', 'group' => 'purchases'],
            ['name' => 'View Sales', 'slug' => 'view_sales', 'group' => 'sales'],
            ['name' => 'Create Sale / POS', 'slug' => 'create_sale', 'group' => 'sales'],
            ['name' => 'Cancel Sale', 'slug' => 'cancel_sale', 'group' => 'sales'],
            ['name' => 'Manage Repairs', 'slug' => 'manage_repairs', 'group' => 'repairs'],
            ['name' => 'View Reports', 'slug' => 'view_reports', 'group' => 'reports'],
            ['name' => 'Manage Users', 'slug' => 'manage_users', 'group' => 'users'],
            ['name' => 'Manage Settings', 'slug' => 'manage_settings', 'group' => 'settings'],
        ];

        foreach ($permissions as $perm) {
            $created = Permission::create($perm);
            $superAdminRole->permissions()->attach($created->id);
            if (!in_array($perm['slug'], ['manage_users', 'manage_settings'])) {
                $adminRole->permissions()->attach($created->id);
            }
            if (in_array($perm['slug'], ['view_products', 'create_sale', 'view_sales', 'manage_repairs'])) {
                $salesmanRole->permissions()->attach($created->id);
            }
            if (in_array($perm['slug'], ['manage_repairs', 'view_products'])) {
                $techRole->permissions()->attach($created->id);
            }
        }

        // 2. Users
        $adminUser = User::create([
            'name' => 'Shop Owner (Admin)',
            'username' => 'admin',
            'email' => 'admin@mobileshop.com',
            'phone' => '+91 98765 43210',
            'role' => 'super_admin',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);
        $adminUser->roles()->attach($superAdminRole->id);

        $salesUser = User::create([
            'name' => 'Suresh Salesman',
            'username' => 'sales',
            'email' => 'sales@mobileshop.com',
            'phone' => '+91 98765 43211',
            'role' => 'salesman',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);
        $salesUser->roles()->attach($salesmanRole->id);

        $techUser = User::create([
            'name' => 'Vijay Technician',
            'username' => 'tech',
            'email' => 'tech@mobileshop.com',
            'phone' => '+91 98765 43212',
            'role' => 'technician',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);
        $techUser->roles()->attach($techRole->id);

        // 3. Shop Settings
        $settings = [
            'shop_name' => 'MobileCare POS & Repair Centre',
            'shop_tagline' => 'Complete Smartphone Sales, Accessories & Chip-Level Repairs',
            'shop_address' => 'Shop #12, First Floor, City Centre Mall, MG Road, Pune, Maharashtra 411001',
            'shop_phone' => '+91 98765 43210',
            'shop_email' => 'support@mobilecarepos.com',
            'gst_number' => '27ABCDE1234F1Z5',
            'currency_symbol' => '₹',
            'invoice_prefix' => 'INV',
            'purchase_prefix' => 'PUR',
            'repair_prefix' => 'REP',
            'default_tax_percent' => '18',
            'thermal_printer_width' => '80mm',
            'invoice_footer' => 'Thank you for your business! For warranty and support, please visit us with this invoice.',
            'terms_conditions' => "1. Warranty covers manufacturing defects only.\n2. Physical or liquid damages are void from warranty.\n3. Repaired devices must be collected within 30 days.\n4. No return without original bill.",
        ];
        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        // 4. Brands
        $brands = [
            'Apple' => 'apple',
            'Samsung' => 'samsung',
            'OnePlus' => 'oneplus',
            'Xiaomi' => 'xiaomi',
            'Vivo' => 'vivo',
            'Oppo' => 'oppo',
            'Realme' => 'realme',
            'Motorola' => 'motorola',
            'Boat' => 'boat',
        ];
        $brandModels = [];
        foreach ($brands as $name => $slug) {
            $brandModels[$slug] = Brand::create(['name' => $name, 'slug' => $slug, 'status' => 'active']);
        }

        // 5. Categories
        $categories = [
            'Smartphones' => 'smartphones',
            'Feature Phones' => 'feature-phones',
            'Chargers & Adapters' => 'chargers',
            'USB Cables' => 'usb-cables',
            'Earphones & Audio' => 'audio',
            'Power Banks' => 'power-banks',
            'Screen Protectors' => 'screen-protectors',
            'Cases & Covers' => 'cases-covers',
            'Spare Parts' => 'spare-parts',
        ];
        $catModels = [];
        foreach ($categories as $name => $slug) {
            $catModels[$slug] = Category::create(['name' => $name, 'slug' => $slug, 'status' => 'active']);
        }

        // 6. Suppliers
        $sup1 = Supplier::create([
            'name' => 'Star Mobile Distributors',
            'company_name' => 'Star Telecom Pvt Ltd',
            'mobile' => '+91 98220 11223',
            'email' => 'orders@startelecom.com',
            'address' => '45 Lamington Road, Mumbai',
            'gst_number' => '27AAACS1122D1Z9',
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $sup2 = Supplier::create([
            'name' => 'Apex Accessories Hub',
            'company_name' => 'Apex Import Exports',
            'mobile' => '+91 98330 44556',
            'email' => 'sales@apexacc.com',
            'address' => '102 Karol Bagh, New Delhi',
            'gst_number' => '07AAACA4455K1Z1',
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $sup3 = Supplier::create([
            'name' => 'ChipFix Spare Solutions',
            'company_name' => 'ChipFix Technologies',
            'mobile' => '+91 97440 77889',
            'email' => 'spares@chipfix.com',
            'address' => '22 SP Road, Bangalore',
            'gst_number' => '29AAACC7788J1Z3',
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        // 7. Customers
        $walkin = Customer::create([
            'name' => 'Walk-in Customer',
            'mobile' => '9999999999',
            'email' => 'walkin@mobilecarepos.com',
            'address' => 'Store Counter',
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $cust1 = Customer::create([
            'name' => 'Rahul Sharma',
            'mobile' => '9822123456',
            'email' => 'rahul.sharma@example.com',
            'address' => 'Flat 302, Green Valley Apartments, Kothrud, Pune',
            'gst_number' => null,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        $cust2 = Customer::create([
            'name' => 'Priya Patel',
            'mobile' => '9890165432',
            'email' => 'priya.patel@example.com',
            'address' => 'B-14, Shanti Nagar, Viman Nagar, Pune',
            'gst_number' => null,
            'opening_balance' => 0,
            'current_balance' => 0,
        ]);

        // 8. Technicians
        $tech1 = Technician::create([
            'user_id' => $techUser->id,
            'name' => 'Vijay Kumar',
            'mobile' => '+91 98765 43212',
            'email' => 'tech@mobileshop.com',
            'address' => 'Shivajinagar, Pune',
            'specialization' => 'Chip-level IC repair & Motherboard diagnostics',
            'commission_percent' => 10,
            'salary' => 28000,
            'status' => 'active',
        ]);

        $tech2 = Technician::create([
            'name' => 'Rajesh Mehta',
            'mobile' => '+91 98555 88990',
            'email' => 'rajesh.repair@example.com',
            'address' => 'Camp, Pune',
            'specialization' => 'Display laminations, Glass replacement & Battery servicing',
            'commission_percent' => 15,
            'salary' => 24000,
            'status' => 'active',
        ]);

        // 9. Products & Serials
        // Mobile 1: iPhone 15 Pro
        $pIphone = Product::create([
            'sku' => 'IPH15P-128-NT',
            'name' => 'Apple iPhone 15 Pro (128GB - Natural Titanium)',
            'type' => 'mobile',
            'brand_id' => $brandModels['apple']->id,
            'category_id' => $catModels['smartphones']->id,
            'model_no' => 'A3102',
            'barcode' => '195949012345',
            'purchase_price' => 110000.00,
            'selling_price' => 124999.00,
            'wholesale_price' => 121000.00,
            'mrp' => 134900.00,
            'tax_percent' => 18.00,
            'min_stock' => 1,
            'current_stock' => 2,
            'status' => 'active',
            'description' => 'A17 Pro chip, Titanium design, 48MP camera, Action button.',
        ]);

        $iphoneSerial1 = ProductSerial::create([
            'product_id' => $pIphone->id,
            'imei_1' => '356789012345671',
            'imei_2' => '356789012345672',
            'serial_no' => 'F2LW890123',
            'color' => 'Natural Titanium',
            'ram' => '8GB',
            'storage' => '128GB',
            'warranty_months' => 12,
            'purchase_price' => 110000.00,
            'selling_price' => 124999.00,
            'status' => 'available',
        ]);

        $iphoneSerial2 = ProductSerial::create([
            'product_id' => $pIphone->id,
            'imei_1' => '356789012345673',
            'imei_2' => '356789012345674',
            'serial_no' => 'F2LW890124',
            'color' => 'Natural Titanium',
            'ram' => '8GB',
            'storage' => '128GB',
            'warranty_months' => 12,
            'purchase_price' => 110000.00,
            'selling_price' => 124999.00,
            'status' => 'available',
        ]);

        // Mobile 2: Samsung Galaxy S24 Ultra
        $pSamsung = Product::create([
            'sku' => 'SAMS24U-256-GR',
            'name' => 'Samsung Galaxy S24 Ultra 5G (256GB - Titanium Gray)',
            'type' => 'mobile',
            'brand_id' => $brandModels['samsung']->id,
            'category_id' => $catModels['smartphones']->id,
            'model_no' => 'SM-S928B',
            'barcode' => '880609531234',
            'purchase_price' => 104000.00,
            'selling_price' => 119999.00,
            'wholesale_price' => 116000.00,
            'mrp' => 129999.00,
            'tax_percent' => 18.00,
            'min_stock' => 1,
            'current_stock' => 2,
            'status' => 'active',
            'description' => 'Snapdragon 8 Gen 3, S-Pen included, 200MP camera, Galaxy AI.',
        ]);

        $samsungSerial1 = ProductSerial::create([
            'product_id' => $pSamsung->id,
            'imei_1' => '862345098765431',
            'imei_2' => '862345098765432',
            'serial_no' => 'R5CX109876',
            'color' => 'Titanium Gray',
            'ram' => '12GB',
            'storage' => '256GB',
            'warranty_months' => 12,
            'purchase_price' => 104000.00,
            'selling_price' => 119999.00,
            'status' => 'available',
        ]);

        $samsungSerial2 = ProductSerial::create([
            'product_id' => $pSamsung->id,
            'imei_1' => '862345098765433',
            'imei_2' => '862345098765434',
            'serial_no' => 'R5CX109877',
            'color' => 'Titanium Gray',
            'ram' => '12GB',
            'storage' => '256GB',
            'warranty_months' => 12,
            'purchase_price' => 104000.00,
            'selling_price' => 119999.00,
            'status' => 'available',
        ]);

        // Mobile 3: OnePlus 12 5G
        $pOnePlus = Product::create([
            'sku' => 'OP12-256-BK',
            'name' => 'OnePlus 12 5G (256GB - Silky Black)',
            'type' => 'mobile',
            'brand_id' => $brandModels['oneplus']->id,
            'category_id' => $catModels['smartphones']->id,
            'model_no' => 'CPH2581',
            'barcode' => '692181561234',
            'purchase_price' => 54000.00,
            'selling_price' => 64999.00,
            'wholesale_price' => 61000.00,
            'mrp' => 69999.00,
            'tax_percent' => 18.00,
            'min_stock' => 2,
            'current_stock' => 2,
            'status' => 'active',
            'description' => 'Hasselblad 4th Gen Camera, 100W SUPERVOOC, 5400mAh battery.',
        ]);

        $opSerial1 = ProductSerial::create([
            'product_id' => $pOnePlus->id,
            'imei_1' => '869876543210981',
            'imei_2' => '869876543210982',
            'serial_no' => 'OP12BK881',
            'color' => 'Silky Black',
            'ram' => '12GB',
            'storage' => '256GB',
            'warranty_months' => 12,
            'purchase_price' => 54000.00,
            'selling_price' => 64999.00,
            'status' => 'available',
        ]);

        $opSerial2 = ProductSerial::create([
            'product_id' => $pOnePlus->id,
            'imei_1' => '869876543210983',
            'imei_2' => '869876543210984',
            'serial_no' => 'OP12BK882',
            'color' => 'Silky Black',
            'ram' => '12GB',
            'storage' => '256GB',
            'warranty_months' => 12,
            'purchase_price' => 54000.00,
            'selling_price' => 64999.00,
            'status' => 'available',
        ]);

        // Accessories
        $pAppleCharger = Product::create([
            'sku' => 'APL-20W-PWR',
            'name' => 'Apple 20W USB-C Power Adapter Original',
            'type' => 'accessory',
            'brand_id' => $brandModels['apple']->id,
            'category_id' => $catModels['chargers']->id,
            'barcode' => '190199401234',
            'purchase_price' => 1200.00,
            'selling_price' => 1899.00,
            'mrp' => 1900.00,
            'tax_percent' => 18.00,
            'min_stock' => 5,
            'current_stock' => 20,
            'status' => 'active',
            'description' => 'Fast charging adapter compatible with iPhone 12/13/14/15 series.',
        ]);

        $pSamCharger = Product::create([
            'sku' => 'SAM-45W-CHG',
            'name' => 'Samsung 45W Super Fast Charging 2.0 Adapter',
            'type' => 'accessory',
            'brand_id' => $brandModels['samsung']->id,
            'category_id' => $catModels['chargers']->id,
            'barcode' => '880609112345',
            'purchase_price' => 1650.00,
            'selling_price' => 2499.00,
            'mrp' => 2999.00,
            'tax_percent' => 18.00,
            'min_stock' => 5,
            'current_stock' => 15,
            'status' => 'active',
            'description' => 'Original Samsung 45W Type-C adapter with PD 3.0 support.',
        ]);

        $pBoatAudio = Product::create([
            'sku' => 'BOAT-AD141-ANC',
            'name' => 'Boat Airdopes 141 ANC True Wireless Earbuds',
            'type' => 'accessory',
            'brand_id' => $brandModels['boat']->id,
            'category_id' => $catModels['audio']->id,
            'barcode' => '890760511223',
            'purchase_price' => 850.00,
            'selling_price' => 1499.00,
            'mrp' => 2990.00,
            'tax_percent' => 18.00,
            'min_stock' => 5,
            'current_stock' => 25,
            'status' => 'active',
            'description' => '32dB Active Noise Cancellation, 42 hours playback time.',
        ]);

        $pCable = Product::create([
            'sku' => 'CBL-TC-65W',
            'name' => 'Braided 65W Fast Charging USB-C to Type-C Cable (1.5m)',
            'type' => 'accessory',
            'category_id' => $catModels['usb-cables']->id,
            'barcode' => '890123456789',
            'purchase_price' => 90.00,
            'selling_price' => 299.00,
            'mrp' => 499.00,
            'tax_percent' => 18.00,
            'min_stock' => 10,
            'current_stock' => 3, // Low stock demo!
            'status' => 'active',
            'description' => 'Heavy duty braided cable supporting up to 65W fast charging.',
        ]);

        // Spare Parts
        $pIphoneScreen = Product::create([
            'sku' => 'SP-IPH15-OLED',
            'name' => 'iPhone 15 OLED Display Assembly (OEM Quality)',
            'type' => 'spare_part',
            'brand_id' => $brandModels['apple']->id,
            'category_id' => $catModels['spare-parts']->id,
            'purchase_price' => 8500.00,
            'selling_price' => 12500.00,
            'tax_percent' => 18.00,
            'min_stock' => 2,
            'current_stock' => 4,
            'status' => 'active',
            'description' => 'True Tone supported replacement display for iPhone 15.',
        ]);

        $pSamBattery = Product::create([
            'sku' => 'SP-S24-BAT',
            'name' => 'Samsung S24 Ultra Original Replacement Battery 5000mAh',
            'type' => 'spare_part',
            'brand_id' => $brandModels['samsung']->id,
            'category_id' => $catModels['spare-parts']->id,
            'purchase_price' => 1400.00,
            'selling_price' => 2400.00,
            'tax_percent' => 18.00,
            'min_stock' => 3,
            'current_stock' => 6,
            'status' => 'active',
            'description' => 'High capacity OEM replacement battery with zero-cycle count.',
        ]);

        $pChargePort = Product::create([
            'sku' => 'SP-TC-SUBBOARD',
            'name' => 'Universal Type-C Charging Port Board Assembly',
            'type' => 'spare_part',
            'category_id' => $catModels['spare-parts']->id,
            'purchase_price' => 120.00,
            'selling_price' => 650.00,
            'tax_percent' => 18.00,
            'min_stock' => 5,
            'current_stock' => 35,
            'status' => 'active',
            'description' => 'Type-C charging flex board with microphone & antenna connector.',
        ]);

        // Inventory Transactions for initial stock
        $initialProducts = [$pIphone, $pSamsung, $pOnePlus, $pAppleCharger, $pSamCharger, $pBoatAudio, $pCable, $pIphoneScreen, $pSamBattery, $pChargePort];
        foreach ($initialProducts as $prod) {
            InventoryTransaction::create([
                'product_id' => $prod->id,
                'transaction_type' => 'PURCHASE',
                'quantity' => $prod->current_stock,
                'before_stock' => 0,
                'after_stock' => $prod->current_stock,
                'unit_cost' => $prod->purchase_price,
                'notes' => 'Opening / Initial inventory balance',
                'user_id' => $adminUser->id,
            ]);
        }

        // 10. Expenses & Categories
        $expCat1 = ExpenseCategory::create(['name' => 'Shop Rent', 'description' => 'Monthly commercial store lease']);
        $expCat2 = ExpenseCategory::create(['name' => 'Electricity & Power', 'description' => 'Store electricity bill']);
        $expCat3 = ExpenseCategory::create(['name' => 'Staff Salaries', 'description' => 'Monthly employee compensation']);
        $expCat4 = ExpenseCategory::create(['name' => 'Internet & Software', 'description' => 'High speed fiber & software licenses']);
        $expCat5 = ExpenseCategory::create(['name' => 'Tea, Snacks & Refreshments', 'description' => 'Daily staff and customer refreshments']);

        Expense::create([
            'expense_category_id' => $expCat1->id,
            'amount' => 30000.00,
            'date' => now()->startOfMonth()->toDateString(),
            'payment_method' => 'bank_transfer',
            'description' => 'Commercial Shop Rent for current month',
            'created_by' => $adminUser->id,
        ]);

        Expense::create([
            'expense_category_id' => $expCat2->id,
            'amount' => 3450.00,
            'date' => now()->subDays(5)->toDateString(),
            'payment_method' => 'upi',
            'description' => 'Electricity bill payment for current month',
            'created_by' => $adminUser->id,
        ]);

        // 11. Sample Purchases
        $purchase = Purchase::create([
            'purchase_no' => 'PUR-2026-000001',
            'supplier_id' => $sup1->id,
            'purchase_date' => now()->subDays(10)->toDateString(),
            'supplier_invoice_no' => 'STAR-INV-9921',
            'subtotal' => 214000.00,
            'tax_amount' => 0,
            'discount_amount' => 4000.00,
            'grand_total' => 210000.00,
            'paid_amount' => 150000.00,
            'due_amount' => 60000.00,
            'payment_status' => 'partial',
            'payment_method' => 'bank_transfer',
            'notes' => 'Bulk intake for Apple and Samsung flagship models',
            'created_by' => $adminUser->id,
        ]);

        PurchasePayment::create([
            'purchase_id' => $purchase->id,
            'supplier_id' => $sup1->id,
            'payment_date' => now()->subDays(10)->toDateString(),
            'amount' => 150000.00,
            'payment_method' => 'bank_transfer',
            'reference_no' => 'NEFT99881122',
            'notes' => 'Advance RTGS payment',
            'created_by' => $adminUser->id,
        ]);
        $sup1->current_balance = 60000.00;
        $sup1->save();

        // 12. Sample Sale
        $sale = Sale::create([
            'invoice_no' => 'INV-2026-000001',
            'customer_id' => $cust1->id,
            'customer_name' => $cust1->name,
            'customer_mobile' => $cust1->mobile,
            'sale_date' => now()->subDays(2)->toDateString(),
            'subtotal' => 2198.00,
            'discount_type' => 'fixed',
            'discount_amount' => 100.00,
            'tax_percent' => 0.00,
            'tax_amount' => 0.00,
            'grand_total' => 2098.00,
            'paid_amount' => 2098.00,
            'due_amount' => 0.00,
            'change_amount' => 0.00,
            'payment_status' => 'paid',
            'payment_method' => 'upi',
            'status' => 'completed',
            'notes' => 'Customer purchased charger and high-speed cable',
            'created_by' => $salesUser->id,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $pAppleCharger->id,
            'quantity' => 1,
            'unit_price' => 1899.00,
            'unit_cost' => 1200.00,
            'subtotal' => 1899.00,
            'warranty_months' => 6,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $pCable->id,
            'quantity' => 1,
            'unit_price' => 299.00,
            'unit_cost' => 90.00,
            'subtotal' => 299.00,
            'warranty_months' => 3,
        ]);

        SalePayment::create([
            'sale_id' => $sale->id,
            'customer_id' => $cust1->id,
            'payment_date' => now()->subDays(2)->toDateString(),
            'amount' => 2098.00,
            'payment_method' => 'upi',
            'transaction_ref' => 'UPI/GPay/4499112233',
            'notes' => 'Received via QR scan',
            'created_by' => $salesUser->id,
        ]);

        // 13. Sample Repair Jobs across statuses
        // Job 1: Received / Under Diagnosis
        $rep1 = RepairJob::create([
            'repair_no' => 'REP-2026-000001',
            'customer_id' => $cust2->id,
            'customer_name' => $cust2->name,
            'customer_mobile' => $cust2->mobile,
            'brand_id' => $brandModels['samsung']->id,
            'model_name' => 'Samsung Galaxy A53 5G',
            'imei' => '359988776655441',
            'color' => 'Awesome Blue',
            'problem_complaint' => 'Not charging when cable plugged in; loose charging port socket.',
            'physical_condition' => 'Minor scratches on back cover; screen glass intact.',
            'accessories_received' => 'Device only (no SIM or memory card)',
            'estimated_cost' => 850.00,
            'final_cost' => 850.00,
            'advance_amount' => 200.00,
            'paid_amount' => 200.00,
            'due_amount' => 650.00,
            'technician_id' => $tech1->id,
            'status' => 'Under Diagnosis',
            'received_date' => now()->subHours(6),
            'expected_delivery_date' => now()->addDay()->toDateString(),
            'warranty_days' => 30,
            'created_by' => $salesUser->id,
        ]);

        RepairStatusLog::create([
            'repair_job_id' => $rep1->id,
            'from_status' => 'Received',
            'to_status' => 'Under Diagnosis',
            'notes' => 'Technician Vijay opened device for microscopic examination.',
            'created_by' => $techUser->id,
        ]);

        RepairPayment::create([
            'repair_job_id' => $rep1->id,
            'payment_date' => now()->toDateString(),
            'amount' => 200.00,
            'payment_type' => 'advance',
            'payment_method' => 'cash',
            'notes' => 'Advance intake token payment',
            'created_by' => $salesUser->id,
        ]);

        // Job 2: Ready for Delivery
        $rep2 = RepairJob::create([
            'repair_no' => 'REP-2026-000002',
            'customer_id' => $cust1->id,
            'customer_name' => $cust1->name,
            'customer_mobile' => $cust1->mobile,
            'brand_id' => $brandModels['apple']->id,
            'model_name' => 'iPhone 13',
            'imei' => '351122334455667',
            'color' => 'Midnight Black',
            'problem_complaint' => 'Display touch not responding after drop; display cracked.',
            'physical_condition' => 'Cracked front glass, dents on top left corner.',
            'accessories_received' => 'Device with back silicone cover',
            'estimated_cost' => 9500.00,
            'final_cost' => 9500.00,
            'advance_amount' => 3000.00,
            'paid_amount' => 3000.00,
            'due_amount' => 6500.00,
            'technician_id' => $tech2->id,
            'status' => 'Ready for Delivery',
            'received_date' => now()->subDays(1),
            'expected_delivery_date' => now()->toDateString(),
            'completed_date' => now()->subHours(2),
            'warranty_days' => 90,
            'notes' => 'New display tested; Face ID and TrueTone verified working.',
            'created_by' => $salesUser->id,
        ]);

        RepairStatusLog::create([
            'repair_job_id' => $rep2->id,
            'from_status' => 'Repairing',
            'to_status' => 'Ready for Delivery',
            'notes' => 'Display fitted and tested for 2 hours. Notification sent to customer.',
            'created_by' => $techUser->id,
        ]);

        RepairPayment::create([
            'repair_job_id' => $rep2->id,
            'payment_date' => now()->subDays(1)->toDateString(),
            'amount' => 3000.00,
            'payment_type' => 'advance',
            'payment_method' => 'upi',
            'transaction_ref' => 'UPI/GPay/9988112233',
            'notes' => 'Advance payment',
            'created_by' => $salesUser->id,
        ]);

        // Job 3: Delivered
        $rep3 = RepairJob::create([
            'repair_no' => 'REP-2026-000003',
            'customer_id' => $cust2->id,
            'customer_name' => $cust2->name,
            'customer_mobile' => $cust2->mobile,
            'brand_id' => $brandModels['oneplus']->id,
            'model_name' => 'OnePlus 9 Pro',
            'imei' => '861122334455998',
            'color' => 'Morning Mist',
            'problem_complaint' => 'Battery swollen and draining within 2 hours.',
            'physical_condition' => 'Back cover slightly lifted due to battery swelling.',
            'accessories_received' => 'Device only',
            'estimated_cost' => 2200.00,
            'final_cost' => 2200.00,
            'advance_amount' => 500.00,
            'paid_amount' => 2200.00,
            'due_amount' => 0.00,
            'technician_id' => $tech1->id,
            'status' => 'Delivered',
            'received_date' => now()->subDays(3),
            'expected_delivery_date' => now()->subDays(2)->toDateString(),
            'completed_date' => now()->subDays(2),
            'delivered_date' => now()->subDays(1),
            'warranty_days' => 180,
            'notes' => 'Delivered to customer with 6 months battery replacement warranty.',
            'created_by' => $salesUser->id,
        ]);

        RepairPayment::create([
            'repair_job_id' => $rep3->id,
            'payment_date' => now()->subDays(3)->toDateString(),
            'amount' => 500.00,
            'payment_type' => 'advance',
            'payment_method' => 'cash',
            'created_by' => $salesUser->id,
        ]);

        RepairPayment::create([
            'repair_job_id' => $rep3->id,
            'payment_date' => now()->subDays(1)->toDateString(),
            'amount' => 1700.00,
            'payment_type' => 'final',
            'payment_method' => 'cash',
            'notes' => 'Final balance paid upon delivery',
            'created_by' => $salesUser->id,
        ]);

        ActivityLog::log('SEEDED', 'system', null, 'System database seeded with production-ready mobile shop data.');
    }
}
