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
        Schema::table('expenses', function (Blueprint $table) {
            // We are renaming the old 'receipt_url' to 'receipt_path' for clarity
            // and adding it after the 'vendor' column.
            $table->dropColumn('receipt_url');
            $table->string('receipt_path')->nullable()->after('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('receipt_path');
            $table->string('receipt_url')->nullable(); // Add back the old column on rollback
        });
    }
};
