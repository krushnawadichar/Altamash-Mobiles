<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\RepairJob;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileShopSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_login_screen_renders_and_admin_authenticates()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $loginResponse = $this->post('/login', [
            'login' => 'admin@mobileshop.com',
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_dashboard_renders_successfully()
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Pending Repairs');
    }

    public function test_pos_search_and_product_catalog()
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/pos');
        $response->assertStatus(200);

        $searchResponse = $this->actingAs($admin)->getJson('/admin/pos/search?query=iPhone');
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonStructure([
            'products',
            'exact_imei_match'
        ]);
    }

    public function test_pos_sale_checkout_deducts_stock_and_marks_imei_sold()
    {
        $admin = User::where('username', 'admin')->first();
        $customer = Customer::first();
        
        // Find an available phone serial
        $serial = ProductSerial::where('status', 'available')->first();
        $this->assertNotNull($serial, 'Must have at least one available phone serial');
        $product = $serial->product;
        $initialStock = $product->current_stock;

        $payload = [
            'customer_id' => $customer->id,
            'subtotal' => $product->selling_price,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => $product->selling_price,
            'paid_amount' => $product->selling_price,
            'payment_method' => 'cash',
            'notes' => 'Test POS Transaction',
            'items' => [
                [
                    'product_id' => $product->id,
                    'product_serial_id' => $serial->id,
                    'imei' => $serial->imei_1,
                    'quantity' => 1,
                    'unit_price' => $product->selling_price,
                ]
            ],
            'payments' => [
                [
                    'method' => 'cash',
                    'amount' => $product->selling_price
                ]
            ]
        ];

        $checkoutResponse = $this->actingAs($admin)->postJson('/admin/pos', $payload);
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertJsonStructure([
            'success',
            'invoice_no',
            'sale_id',
            'redirect_url'
        ]);

        // Verify serial status became sold
        $serial->refresh();
        $this->assertEquals('sold', $serial->status);
        $this->assertNotNull($serial->sold_at);

        // Verify stock decremented by 1
        $product->refresh();
        $this->assertEquals($initialStock - 1, $product->current_stock);

        // Verify universal ledger transaction
        $this->assertDatabaseHas('inventory_transactions', [
            'product_id' => $product->id,
            'transaction_type' => 'SALE',
            'quantity' => -1,
        ]);
    }

    public function test_repair_intake_and_spare_part_consumption()
    {
        $admin = User::where('username', 'admin')->first();

        $testImei = '359871' . rand(100000000, 999999999);
        $intakePayload = [
            'customer_name' => 'Amit Sharma',
            'customer_mobile' => '9820011223',
            'brand_id' => Brand::first()->id,
            'model_name' => 'Galaxy S21 5G',
            'color' => 'Phantom Gray',
            'imei' => $testImei,
            'problem_complaint' => 'Screen glass broken, touchscreen working',
            'physical_condition' => 'Cracked front glass',
            'accessories_received' => 'No charger, with case',
            'estimated_cost' => 4500,
            'advance_amount' => 1000,
            'payment_method' => 'upi',
            'warranty_days' => 90
        ];

        $repairResponse = $this->actingAs($admin)->post('/admin/repairs', $intakePayload);
        $repairResponse->assertRedirect();

        $repair = RepairJob::where('imei', $testImei)->first();
        $this->assertNotNull($repair);
        $this->assertEquals(1000, $repair->advance_amount);
        $this->assertEquals(3500, $repair->due_amount);

        // 2. Consume spare part from inventory
        $sparePart = Product::where('type', 'spare_part')->first();
        if ($sparePart) {
            $partInitialStock = $sparePart->current_stock;
            $partResponse = $this->actingAs($admin)->post("/admin/repairs/{$repair->id}/add-part", [
                'product_id' => $sparePart->id,
                'quantity' => 1,
                'unit_price' => $sparePart->selling_price
            ]);
            $partResponse->assertRedirect();

            // Verify stock reduced
            $sparePart->refresh();
            $this->assertEquals($partInitialStock - 1, $sparePart->current_stock);

            // Verify REPAIR_PART_USED transaction logged
            $this->assertDatabaseHas('inventory_transactions', [
                'product_id' => $sparePart->id,
                'transaction_type' => 'REPAIR_PART_USED',
                'quantity' => -1
            ]);
        }
    }

    public function test_all_reports_render_successfully()
    {
        $admin = User::where('username', 'admin')->first();

        $reports = [
            '/admin/reports/sales',
            '/admin/reports/purchases',
            '/admin/reports/inventory',
            '/admin/reports/mobiles',
            '/admin/reports/repairs',
            '/admin/reports/profit-loss',
        ];

        foreach ($reports as $uri) {
            $response = $this->actingAs($admin)->get($uri);
            $response->assertStatus(200, "Failed asserting that {$uri} returned 200 OK");
        }
    }

    public function test_settings_can_be_saved()
    {
        $admin = User::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'shop_name' => 'MobileCare Flagship Store',
            'shop_tagline' => 'Fastest Repairs & Best Deals',
            'shop_phone' => '9988776655',
            'shop_email' => 'support@mobilecare.com',
            'default_tax_percent' => 18,
            'low_stock_threshold' => 4,
            'terms_conditions' => 'Standard 30-day warranty on all repairs.',
            'invoice_footer' => 'Thank you for shopping with us!'
        ]);

        $response->assertRedirect();
        $this->assertEquals('MobileCare Flagship Store', \App\Models\Setting::get('shop_name'));
    }
}
