<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campus_id')->constrained('campuses')->onDelete('restrict');
            $table->foreignId('student_category_id')->constrained('student_categories')->onDelete('restrict');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('grade');
            $table->string('admission_number')->unique();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('students');
    }
};
