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
        // This table defines job roles and their base salaries for non-system users.
        Schema::create('employee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Senior Teacher", "Junior Cleaner"
            $table->decimal('base_salary', 10, 2);
            $table->timestamps();
        });

        // This table holds the master record for each monthly salary payment.
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year'); // e.g., 2025
            $table->string('status')->default('unpaid'); // unpaid, paid
            $table->decimal('base_salary', 10, 2);
            $table->decimal('total_earnings', 10, 2);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2);
            $table->timestamps();
        });

        // This table stores individual earnings (bonuses) or deductions for a payslip.
        Schema::create('payslip_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payslip_id')->constrained()->onDelete('cascade');
            $table->string('description'); // e.g., "Overtime Bonus", "Tax Deduction"
            $table->enum('type', ['earning', 'deduction']);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslip_items');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('employee_categories');
    }
};
