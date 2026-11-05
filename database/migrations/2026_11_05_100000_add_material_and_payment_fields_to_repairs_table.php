<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->decimal('material_cost', 10, 2)->default(0)->after('advance_payment');
            $table->decimal('service_charge', 10, 2)->default(0)->after('material_cost');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('service_charge');
            $table->decimal('due_amount', 10, 2)->default(0)->after('paid_amount');
            $table->decimal('profit', 10, 2)->default(0)->after('due_amount');
            $table->string('payment_status')->default('unpaid')->after('profit');
            $table->string('payment_method')->nullable()->after('payment_status');
        });

        Schema::create('repair_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_id')->constrained('repairs')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_items');

        Schema::table('repairs', function (Blueprint $table) {
            $table->dropColumn([
                'material_cost',
                'service_charge',
                'paid_amount',
                'due_amount',
                'profit',
                'payment_status',
                'payment_method',
            ]);
        });
    }
};
