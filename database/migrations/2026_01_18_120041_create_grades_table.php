<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Grades Table
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('next_grade_id')->nullable()->constrained('grades')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Add columns to Students Table
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('grade_id')->nullable()->after('last_name')->constrained('grades')->onDelete('restrict');
            $table->string('status')->default('active')->after('admission_number'); // active, graduated, transferred, etc.
        });

        // 3. Migrate Data
        $students = DB::table('students')->select('id', 'grade')->get();
        $uniqueGrades = $students->pluck('grade')->unique()->filter();

        foreach ($uniqueGrades as $gradeName) {
            // Create Grade
            $gradeId = DB::table('grades')->insertGetId([
                'name' => $gradeName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update students with this grade
            DB::table('students')->where('grade', $gradeName)->update(['grade_id' => $gradeId]);
        }

        // 4. Drop old column (Commented out for safety initially, but I will do it as per plan)
        // Schema::table('students', function (Blueprint $table) {
        //     $table->dropColumn('grade');
        // });
        // Actually, let's keep it for a moment just in case, but usually we drop it.
        // I will drop it to force usage of grade_id.
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restore 'grade' column
        Schema::table('students', function (Blueprint $table) {
            $table->string('grade')->nullable();
        });

        // 2. Restore data (reverse migration)
        $students = DB::table('students')->get();
        foreach ($students as $student) {
            if ($student->grade_id) {
                $gradeName = DB::table('grades')->where('id', $student->grade_id)->value('name');
                DB::table('students')->where('id', $student->id)->update(['grade' => $gradeName]);
            }
        }

        // 3. Drop columns
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['grade_id']);
            $table->dropColumn(['grade_id', 'status']);
        });

        // 4. Drop table
        Schema::dropIfExists('grades');
    }
};
