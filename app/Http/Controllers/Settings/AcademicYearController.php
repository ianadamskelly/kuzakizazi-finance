<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicYearController extends Controller
{
    /**
     * Update academic year settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'academic_year_start' => 'required|date',
            'academic_year_end' => 'required|date|after:academic_year_start',
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'academic_year_start'],
            ['value' => $request->academic_year_start]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'academic_year_end'],
            ['value' => $request->academic_year_end]
        );

        return back()->with('success', 'Academic year settings updated.');
    }

    /**
     * Graduate students to the next class.
     */
    public function graduate(Request $request)
    {
        // Simple authorization check (ensure only super admin or admin)
        // abort_unless(auth()->user()->hasRole('Super Admin'), 403);

        $promotedCount = 0;
        $graduatedCount = 0;

        DB::transaction(function () use (&$promotedCount, &$graduatedCount) {
            $students = Student::active()->with('grade')->get();

            foreach ($students as $student) {
                if (!$student->grade)
                    continue;

                $currentGrade = $student->grade;
                $nextGradeId = $currentGrade->next_grade_id;

                if ($nextGradeId) {
                    // Promote to next grade
                    $student->update(['grade_id' => $nextGradeId]);
                    $promotedCount++;
                } else {
                    // No next grade -> Graduate
                    $student->update(['status' => 'graduated']);
                    $graduatedCount++;
                }
            }
        });

        Log::info("Graduation completed: $promotedCount promoted, $graduatedCount graduated.");

        return back()->with('success', "Graduation completed! $promotedCount students promoted, $graduatedCount graduated.");
    }
}
