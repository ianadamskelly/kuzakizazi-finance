<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\StudentCategory;
use Illuminate\Http\Request;

class StudentCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage settings');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:student_categories,name',
            'description' => 'nullable|string',
        ]);

        StudentCategory::create($request->all());

        return back()->with('success', 'Student category created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentCategory $studentCategory)
    {
        if ($studentCategory->students()->exists()) {
            return back()->with('error', 'Cannot delete category. It is currently assigned to students.');
        }
        
        $studentCategory->delete();

        return back()->with('success', 'Student category deleted successfully.');
    }
}