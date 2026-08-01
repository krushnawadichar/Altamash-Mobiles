<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->morphs('inventoriable');
            $table->string('type'); // purchase, sale, adjustment, transfer, damage, lost
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('remarks')->nullable();
            $table->foreignId('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['inventoriable_id', 'inventoriable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};