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
        Schema::table('payments', function (Blueprint $table) {
            // Allow invoice_id to be null to support advance payments
            // that aren't tied to a specific invoice upon creation.
            $table->foreignId('invoice_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // This makes the column non-nullable again on rollback.
            // Note: This might fail if there are records with null invoice_id.
            $table->foreignId('invoice_id')->nullable(false)->change();
        });
    }
};
