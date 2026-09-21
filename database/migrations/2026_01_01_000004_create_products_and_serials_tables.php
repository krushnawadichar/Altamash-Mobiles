<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique()->index();
            $table->string('name');
            $table->string('type')->default('accessory'); // mobile, accessory, spare_part
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('model_no')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->decimal('purchase_price', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->default(0.00);
            $table->decimal('wholesale_price', 12, 2)->nullable();
            $table->decimal('mrp', 12, 2)->nullable();
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->integer('min_stock')->default(5);
            $table->integer('current_stock')->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('imei_1')->unique()->index();
            $table->string('imei_2')->nullable()->index();
            $table->string('serial_no')->nullable()->index();
            $table->string('color')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();
            $table->integer('warranty_months')->default(12);
            $table->decimal('purchase_price', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->unsignedBigInteger('purchase_item_id')->nullable()->index();
            $table->unsignedBigInteger('sale_item_id')->nullable()->index();
            $table->enum('status', ['available', 'sold', 'under_repair', 'damaged', 'returned'])->default('available')->index();
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_serials');
        Schema::dropIfExists('products');
    }
};
