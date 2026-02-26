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
        Schema::table('employees', function (Blueprint $table) {
            // Make user_id nullable to allow for employees without system access.
            $table->foreignId('user_id')->nullable()->change();
            
            // Make campus_id nullable to allow for employees assigned to "All Campuses".
            $table->foreignId('campus_id')->nullable()->change();

            // Add a link to the new employee_categories table.
            $table->foreignId('employee_category_id')->nullable()->after('job_title')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // To make this reversible, we need to handle potential null values.
            // This is a basic rollback; in a real app, you'd need a strategy for orphaned records.
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreignId('campus_id')->nullable(false)->change();

            $table->dropForeign(['employee_category_id']);
            $table->dropColumn('employee_category_id');
        });
    }
};