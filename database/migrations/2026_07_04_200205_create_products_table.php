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
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('imei')->unique()->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('mobile_company_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('gst_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->string('color')->nullable();
            $table->string('storage')->nullable();
            $table->string('ram')->nullable();
            $table->text('description')->nullable();
            $table->integer('minimum_stock')->default(5);
            $table->integer('current_stock')->default(0);
            $table->string('image')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['sku', 'barcode', 'imei']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};