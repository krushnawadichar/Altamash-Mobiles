<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_serial_id')->nullable()->constrained('product_serials')->nullOnDelete();
            $table->enum('transaction_type', [
                'PURCHASE',
                'SALE',
                'SALE_RETURN',
                'PURCHASE_RETURN',
                'REPAIR_PART_USED',
                'STOCK_ADJUSTMENT_IN',
                'STOCK_ADJUSTMENT_OUT'
            ])->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->string('reference_type')->nullable(); // e.g. Purchase, Sale, RepairJob, StockAdjustment
            $table->integer('quantity'); // positive for in, negative for out
            $table->integer('before_stock');
            $table->integer('after_stock');
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
