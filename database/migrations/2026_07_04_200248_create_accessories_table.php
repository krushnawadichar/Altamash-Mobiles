<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Cover, Tempered Glass, Charger, etc.
            $table->string('sku')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->decimal('gst_percentage', 5, 2)->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(5);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['sku', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};