<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add missing balance columns if they don't exist
            if (!Schema::hasColumn('students', 'transport_balance')) {
                $table->decimal('transport_balance', 12, 2)->default(0.00)->after('uniform_balance');
            }
            if (!Schema::hasColumn('students', 'others_balance')) {
                $table->decimal('others_balance', 12, 2)->default(0.00)->after('transport_balance');
            }
        });

        // Ensure 'transport' and 'others' are allowed in ENUMs
        // Note: altering enums is database specific and can be tricky. 
        // For standard MySQL/MariaDB, we can modify the column.
        // Doing this raw to be safe, assuming user is on MySQL/MariaDB

        // Fee Structures
        DB::statement("ALTER TABLE fee_structures MODIFY COLUMN category ENUM('fees', 'food', 'diary', 'assessment', 'uniform', 'transport', 'others') DEFAULT 'fees'");

        // Payments
        DB::statement("ALTER TABLE payments MODIFY COLUMN category ENUM('fees', 'food', 'diary', 'assessment', 'uniform', 'transport', 'others') DEFAULT 'fees'");

        // Student Ledgers
        DB::statement("ALTER TABLE student_ledgers MODIFY COLUMN category ENUM('fees', 'food', 'diary', 'assessment', 'uniform', 'transport', 'others')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['transport_balance', 'others_balance']);
        });

        // Reverting enums is risky if data exists, so we generally skip strictly reverting the enum expansion to avoid data loss.
    }
};
