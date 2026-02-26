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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'received_by')) {
                $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null')->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['reference_no', 'received_by']);
        });
    }
};
