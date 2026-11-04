<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StorefrontAndAdminFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed basic permissions and admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Permission::firstOrCreate(['name' => 'view dashboard']);
    }

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('ALTAMASH MOBILE');
    }

    public function test_shop_page_renders_and_filters_properly(): void
    {
        $category = Category::create(['name' => 'Smartphones', 'slug' => 'smartphones', 'status' => 1]);
        $product = Product::create([
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'sku' => 'IPH15P',
            'selling_price' => 129999,
            'category_id' => $category->id,
            'status' => 1,
            'stock_quantity' => 10,
        ]);

        $inactiveProduct = Product::create([
            'name' => 'Discontinued Phone',
            'slug' => 'discontinued-phone',
            'sku' => 'DISC01',
            'selling_price' => 5000,
            'category_id' => $category->id,
            'status' => 0,
            'stock_quantity' => 0,
        ]);

        // Visit shop without filters
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('iPhone 15 Pro');
        $response->assertDontSee('Discontinued Phone');

        // Search for product
        $searchResponse = $this->get('/shop?q=iPhone');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('iPhone 15 Pro');

        // Search for non-existent product
        $noResultResponse = $this->get('/shop?q=NonExistentDevice123');
        $noResultResponse->assertStatus(200);
        $noResultResponse->assertSee('No products found');
    }

    public function test_contact_form_submits_successfully(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Aarav Sharma',
            'email' => 'aarav@example.com',
            'subject' => 'Repair Service',
            'message' => 'I would like to inquire about battery replacement.',
        ]);

        $response->assertSessionHas('success');
        $response->assertRedirect();
    }

    public function test_cart_operations_and_stock_limits(): void
    {
        $category = Category::create(['name' => 'Accessories', 'slug' => 'accessories', 'status' => 1]);
        $product = Product::create([
            'name' => 'Wireless Earbuds',
            'slug' => 'wireless-earbuds',
            'sku' => 'EARB01',
            'selling_price' => 1999,
            'category_id' => $category->id,
            'status' => 1,
            'stock_quantity' => 3,
        ]);

        // Add 2 items (valid)
        $addResponse = $this->post(route('cart.add', $product->id), ['quantity' => 2]);
        $addResponse->assertSessionHas('success');
        $this->assertEquals(2, session('cart')[$product->id]['quantity']);

        // Try to add 2 more (exceeds stock of 3)
        $overstockResponse = $this->post(route('cart.add', $product->id), ['quantity' => 2]);
        $overstockResponse->assertSessionHas('error');
        $this->assertEquals(2, session('cart')[$product->id]['quantity']);

        // Update quantity to 1
        $updateResponse = $this->patch(route('cart.update'), [
            'id' => $product->id,
            'quantity' => 1,
        ]);
        $updateResponse->assertSessionHas('success');
        $this->assertEquals(1, session('cart')[$product->id]['quantity']);

        // Remove item from cart
        $removeResponse = $this->delete(route('cart.remove'), ['id' => $product->id]);
        $removeResponse->assertSessionHas('success');
        $this->assertEmpty(session('cart'));
    }

    public function test_checkout_flow_and_public_invoice(): void
    {
        $category = Category::create(['name' => 'Smartphones', 'slug' => 'smartphones', 'status' => 1]);
        $product = Product::create([
            'name' => 'OnePlus 12',
            'slug' => 'oneplus-12',
            'sku' => 'OP12-256',
            'selling_price' => 64999,
            'category_id' => $category->id,
            'status' => 1,
            'stock_quantity' => 5,
        ]);

        // Put item into session cart
        session()->put('cart', [
            $product->id => [
                'name' => $product->name,
                'quantity' => 2,
                'price' => $product->selling_price,
                'image' => null,
            ]
        ]);

        // Process checkout
        $checkoutResponse = $this->post(route('checkout.process'), [
            'name' => 'Rohit Verma',
            'phone' => '9876543210',
            'email' => 'rohit@example.com',
            'address' => '45 Civil Lines',
            'city' => 'Nagpur',
            'state' => 'Maharashtra',
            'payment_method' => 'Cash on Delivery',
        ]);

        $checkoutResponse->assertRedirect(route('checkout.success'));
        $checkoutResponse->assertSessionHas('invoice_number');

        $invoiceNumber = session('invoice_number');
        $this->assertNotNull($invoiceNumber);

        // Verify product stock reduced from 5 to 3
        $this->assertEquals(3, $product->fresh()->stock_quantity);

        // Verify sale created in database
        $sale = Sale::where('invoice_number', $invoiceNumber)->first();
        $this->assertNotNull($sale);
        $this->assertEquals(129998, $sale->grand_total);
        $this->assertEquals('unpaid', $sale->payment_status);

        // Verify public invoice view is accessible
        $invoiceResponse = $this->get(route('invoice.public', $invoiceNumber));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee($invoiceNumber);
        $invoiceResponse->assertSee('OnePlus 12');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        // Guest redirected to login
        $guestResponse = $this->get(route('admin.dashboard'));
        $guestResponse->assertRedirect(route('login'));

        // Normal authenticated user gets 403 Forbidden
        $authenticatedResponse = $this->actingAs($user)->get(route('admin.dashboard'));
        $authenticatedResponse->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_admin_can_create_purchase_with_partial_payment(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $supplier = Supplier::create([
            'name' => 'Apex Mobile Distributors',
            'company_name' => 'Apex Pvt Ltd',
            'phone' => '9988776655',
            'status' => 1,
        ]);

        $category = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'status' => 1,
        ]);

        $product = Product::create([
            'name' => 'Redmi Note 13',
            'slug' => 'redmi-note-13',
            'sku' => 'RDM13',
            'purchase_price' => 12000,
            'selling_price' => 15000,
            'stock_quantity' => 5,
            'category_id' => $category->id,
            'status' => 1,
        ]);

        $purchaseData = [
            'supplier_id' => $supplier->id,
            'purchase_date' => now()->toDateString(),
            'invoice_number' => 'INV-APEX-101',
            'products' => [$product->id],
            'prices' => [12000],
            'quantities' => [2],
            'subtotal' => 24000,
            'discount' => 1000,
            'tax' => 0,
            'grand_total' => 23000,
            'paid_amount' => 10000,
            'due_amount' => 13000,
            'payment_method' => 'Bank Transfer',
            'notes' => 'Partial upfront advance paid via NEFT',
        ];

        $response = $this->actingAs($admin)->post(route('admin.purchases.store'), $purchaseData);

        // Check purchase in DB
        $purchase = Purchase::where('invoice_number', 'INV-APEX-101')->first();
        $this->assertNotNull($purchase);
        $response->assertRedirect(route('admin.purchases.show', $purchase));
        $this->assertEquals(23000, $purchase->grand_total);
        $this->assertEquals(10000, $purchase->paid_amount);
        $this->assertEquals(13000, $purchase->due_amount);
        $this->assertEquals('partial', $purchase->payment_status);

        // Check stock updated (5 + 2 = 7)
        $this->assertEquals(7, $product->fresh()->stock_quantity);

        // Check supplier relationship and totals
        $this->assertEquals(23000, $supplier->fresh()->total_purchases);
        $this->assertEquals(13000, $supplier->fresh()->total_due);
    }

    public function test_admin_can_record_supplier_due_payment_and_settle_invoice(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $supplier = Supplier::create([
            'name' => 'Global Electronics',
            'phone' => '8888999900',
            'status' => 1,
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PO-DUE-777',
            'purchase_date' => now(),
            'subtotal' => 20000,
            'tax' => 0,
            'discount' => 0,
            'grand_total' => 20000,
            'paid_amount' => 5000,
            'due_amount' => 15000,
            'payment_status' => 'partial',
            'payment_method' => 'Cash',
            'notes' => 'Initial token payment',
        ]);

        // Step 1: Pay 5,000 partial due
        $step1Response = $this->actingAs($admin)->post(route('admin.purchases.pay-due', $purchase->id), [
            'amount' => 5000,
            'payment_method' => 'UPI',
            'payment_reference' => 'UTR12345678',
            'payment_notes' => 'Second installment via GPay',
        ]);

        $step1Response->assertRedirect();
        $purchase->refresh();
        $this->assertEquals(10000, $purchase->paid_amount);
        $this->assertEquals(10000, $purchase->due_amount);
        $this->assertEquals('partial', $purchase->payment_status);
        $this->assertStringContainsString('UTR12345678', $purchase->notes);

        // Step 2: Settle remaining 10,000 due
        $step2Response = $this->actingAs($admin)->post(route('admin.purchases.pay-due', $purchase->id), [
            'amount' => 10000,
            'payment_method' => 'Bank Transfer',
            'payment_reference' => 'IMPS998877',
            'payment_notes' => 'Full clearance settlement',
        ]);

        $step2Response->assertRedirect();
        $purchase->refresh();
        $this->assertEquals(20000, $purchase->paid_amount);
        $this->assertEquals(0, $purchase->due_amount);
        $this->assertEquals('paid', $purchase->payment_status);
        $this->assertStringContainsString('Full clearance settlement', $purchase->notes);
    }

    public function test_supplier_due_payment_rejects_exceeding_amount(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $supplier = Supplier::create([
            'name' => 'Reliable Vendors',
            'status' => 1,
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PO-EXCEED-1',
            'purchase_date' => now(),
            'subtotal' => 5000,
            'grand_total' => 5000,
            'paid_amount' => 3000,
            'due_amount' => 2000,
            'payment_status' => 'partial',
        ]);

        // Attempt to pay 3000 when only 2000 is due
        $response = $this->actingAs($admin)->post(route('admin.purchases.pay-due', $purchase->id), [
            'amount' => 3000,
            'payment_method' => 'Cash',
        ]);

        $response->assertSessionHasErrors(['amount']);
        $this->assertEquals(2000, $purchase->fresh()->due_amount);
    }

    public function test_purchase_print_view_renders_successfully(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $supplier = Supplier::create([
            'name' => 'Alpha Distributors',
            'status' => 1,
        ]);

        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'invoice_number' => 'PO-PRINT-123',
            'purchase_date' => now(),
            'subtotal' => 10000,
            'grand_total' => 10000,
            'paid_amount' => 10000,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.purchases.print', $purchase->id));
        $response->assertStatus(200);
        $response->assertSee('PO-PRINT-123');
        $response->assertSee('Alpha Distributors');
        $response->assertSee('PURCHASE BILL');
    }
}

