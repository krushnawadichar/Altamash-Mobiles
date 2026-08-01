<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('repair_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_mobile');
            $table->string('device_name');
            $table->string('imei')->nullable();
            $table->text('issue');
            $table->text('accessories_received')->nullable();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('advance_paid', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->text('engineer_notes')->nullable();
            $table->foreignId('repair_status_id')->constrained()->onDelete('cascade');
            $table->date('receive_date');
            $table->date('delivery_date')->nullable();
            $table->string('payment_status')->default('pending'); // paid, partial, pending
            $table->text('images')->nullable(); // JSON array
            $table->text('documents')->nullable(); // JSON array
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('repair_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};