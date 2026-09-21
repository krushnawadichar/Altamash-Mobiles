<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('specialization')->nullable();
            $table->decimal('commission_percent', 5, 2)->default(0.00);
            $table->decimal('salary', 10, 2)->default(0.00);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('repair_no')->unique()->index(); // e.g. REP-2026-000001
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_mobile')->index();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('model_name');
            $table->string('imei')->nullable()->index();
            $table->string('serial_no')->nullable()->index();
            $table->string('color')->nullable();
            $table->text('problem_complaint');
            $table->text('physical_condition')->nullable();
            $table->string('accessories_received')->nullable();
            $table->decimal('estimated_cost', 10, 2)->default(0.00);
            $table->decimal('final_cost', 10, 2)->default(0.00);
            $table->decimal('advance_amount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('due_amount', 10, 2)->default(0.00);
            $table->foreignId('technician_id')->nullable()->constrained('technicians')->nullOnDelete();
            $table->enum('status', [
                'Received',
                'Diagnosis Pending',
                'Under Diagnosis',
                'Estimate Given',
                'Customer Approval Pending',
                'Approved',
                'Repairing',
                'Waiting for Parts',
                'Repair Completed',
                'Ready for Delivery',
                'Delivered',
                'Cancelled'
            ])->default('Received')->index();
            $table->dateTime('received_date');
            $table->date('expected_delivery_date')->nullable();
            $table->dateTime('completed_date')->nullable();
            $table->dateTime('delivered_date')->nullable();
            $table->integer('warranty_days')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('repair_parts_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 10, 2)->default(0.00);
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('repair_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->string('payment_type')->default('advance'); // advance, partial, final
            $table->string('payment_method')->default('cash'); // cash, upi, card, bank_transfer
            $table->string('transaction_ref')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('repair_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained()->cascadeOnDelete();
            $table->string('from_status');
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_status_logs');
        Schema::dropIfExists('repair_payments');
        Schema::dropIfExists('repair_parts_used');
        Schema::dropIfExists('repair_jobs');
        Schema::dropIfExists('technicians');
    }
};
