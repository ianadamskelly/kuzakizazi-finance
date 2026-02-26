<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations to implement categorized funds.
     */
    public function up(): void
    {
        // 1. Update Fee Structures to include categories
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->enum('category', ['fees', 'food', 'diary', 'assessment', 'uniform'])
                ->default('fees')
                ->after('student_category_id');
        });

        // 2. Add categorized balances to the Students table
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('fees_balance', 12, 2)->default(0.00)->after('admission_number');
            $table->decimal('food_balance', 12, 2)->default(0.00)->after('fees_balance');
            $table->decimal('diaries_balance', 12, 2)->default(0.00)->after('food_balance');
            $table->decimal('assessment_balance', 12, 2)->default(0.00)->after('diaries_balance');
            $table->decimal('uniform_balance', 12, 2)->default(0.00)->after('assessment_balance');
        });

        // 3. Update Payments to track which fund the money went into
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('category', ['fees', 'food', 'diary', 'assessment', 'uniform'])
                ->default('fees')
                ->after('student_id');
        });

        // 4. Update the Student Ledger table for granular audit trails
        // Since student_ledgers already exists, we modify it instead of creating it
        if (Schema::hasTable('student_ledgers')) {
            Schema::table('student_ledgers', function (Blueprint $table) {
                if (!Schema::hasColumn('student_ledgers', 'category')) {
                    $table->enum('category', ['fees', 'food', 'diary', 'assessment', 'uniform'])->after('student_id');
                }
                // The provided request uses 'type' [debit, credit], existing has it. 
                // Description exists. invoice_id/payment_id exist.
                // We ensure it matches the user's requested structure.
            });
        } else {
            Schema::create('student_ledgers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->enum('category', ['fees', 'food', 'diary', 'assessment', 'uniform']);
                $table->enum('type', ['debit', 'credit']); // debit = charge (invoice), credit = payment
                $table->decimal('amount', 12, 2);
                $table->string('description'); // e.g., "Term 1 Tuition Charge" or "Cash Payment"
                $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('student_ledgers', 'category')) {
            Schema::table('student_ledgers', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['fees_balance', 'food_balance', 'diaries_balance', 'assessment_balance', 'uniform_balance']);
        });

        Schema::table('fee_structures', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
