<?php

namespace App\Http\Controllers;

use App\Models\StudentCategory;
use App\Models\EmployeeCategory;
use App\Models\Campus; // Import Campus model

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage settings');
    }

    /**
     * Display the main settings page with all data.
     */
    public function index()
    {
        $studentCategories = StudentCategory::with('feeStructures')->latest()->get();

        // Prepare data for the bulk edit table (Renamed local variable to avoid confusion, but keeping view key same if needed, though I should check usage)
        // Existing "grades" variable seems to be for student category fee summaries.
        // I will keep it as 'grades' for now to avoid breaking existing view, strictly for fee structure summary.
        $feeGrades = $studentCategories->map(function ($category) {
            $category->tuition_amount = $category->feeStructures->where('category', 'fees')->sum('amount');
            $category->food_amount = $category->feeStructures->where('category', 'food')->sum('amount');
            $category->transport_amount = $category->feeStructures->where('category', 'transport')->sum('amount');
            $category->others_amount = $category->feeStructures->where('category', 'others')->sum('amount');
            return $category;
        });

        $employeeCategories = EmployeeCategory::latest()->get();
        $campuses = Campus::all();

        // New Data for Academic Year & Grades
        $academicGrades = \App\Models\Grade::with('nextGrade')->orderBy('id')->get();
        $academicYearStart = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'academic_year_start')->value('value');
        $academicYearEnd = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'academic_year_end')->value('value');

        return view('settings.index', [
            'studentCategories' => $studentCategories,
            'grades' => $feeGrades, // Keeping existing key
            'employeeCategories' => $employeeCategories,
            'campuses' => $campuses,
            'academicGrades' => $academicGrades,
            'academicYearStart' => $academicYearStart,
            'academicYearEnd' => $academicYearEnd,
        ]);
    }
}