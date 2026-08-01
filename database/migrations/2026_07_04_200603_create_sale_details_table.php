<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->morphs('sellable'); // product or accessory
            $table->integer('quantity');
            $table->decimal('selling_price', 15, 2);
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('profit', 15, 2);
            $table->string('imei')->nullable();
            $table->string('serial_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};