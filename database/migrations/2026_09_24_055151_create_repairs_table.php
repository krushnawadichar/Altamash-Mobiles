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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->string('repair_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('device_name');
            $table->text('problem_description');
            $table->date('received_date');
            $table->date('expected_delivery')->nullable();
            $table->decimal('estimated_cost', 10, 2)->default(0);
            $table->decimal('final_cost', 10, 2)->default(0);
            $table->decimal('advance_payment', 10, 2)->default(0);
            $table->string('status')->default('Received');
            $table->text('technician_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
