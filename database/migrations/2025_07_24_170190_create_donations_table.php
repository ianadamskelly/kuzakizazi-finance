<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('set null');
            $table->string('donor_name');
            $table->decimal('amount', 10, 2);
            $table->date('donation_date');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('donations');
    }
};
