<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Store a newly created grade.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:grades,name',
            'next_grade_id' => 'nullable|exists:grades,id',
        ]);

        Grade::create($request->all());

        return back()->with('success', 'Grade created successfully.');
    }

    /**
     * Update the specified grade.
     */
    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:grades,name,' . $grade->id,
            'next_grade_id' => 'nullable|exists:grades,id|different:id', // Cannot be next grade to itself
        ]);

        $grade->update($request->all());

        return back()->with('success', 'Grade updated successfully.');
    }

    /**
     * Remove the specified grade.
     */
    public function destroy(Grade $grade)
    {
        if ($grade->students()->exists()) {
            return back()->with('error', 'Cannot delete grade because it has associated students.');
        }

        $grade->delete();

        return back()->with('success', 'Grade deleted successfully.');
    }
}
