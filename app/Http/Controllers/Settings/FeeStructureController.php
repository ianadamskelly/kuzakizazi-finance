<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\StudentCategory;
use Illuminate\Http\Request;

class FeeStructureController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, StudentCategory $studentCategory)
    {
        $this->authorize('manage settings');

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $studentCategory->feeStructures()->create($request->all());

        return back()->with('success', 'Fee item added successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeeStructure $feeStructure)
    {
        $this->authorize('manage settings');

        $feeStructure->delete();

        return back()->with('success', 'Fee item deleted successfully.');
    }

    /**
     * Update multiple fee structures at once.
     */
    public function bulkUpdate(Request $request)
    {
        $this->authorize('manage settings');

        $data = $request->validate([
            'fees' => 'required|array',
            'fees.*.tuition' => 'nullable|numeric|min:0',
            'fees.*.food' => 'nullable|numeric|min:0',
            'fees.*.transport' => 'nullable|numeric|min:0',
            'fees.*.others' => 'nullable|numeric|min:0',
        ]);

        foreach ($data['fees'] as $gradeId => $amounts) {
            // Tuition
            FeeStructure::updateOrCreate(
                ['student_category_id' => $gradeId, 'category' => 'fees'],
                ['amount' => $amounts['tuition'] ?? 0, 'description' => 'Tuition Fees', 'name' => 'Tuition']
            );
            // Food
            FeeStructure::updateOrCreate(
                ['student_category_id' => $gradeId, 'category' => 'food'],
                ['amount' => $amounts['food'] ?? 0, 'description' => 'Canteen & Food', 'name' => 'Food']
            );
            // Transport
            FeeStructure::updateOrCreate(
                ['student_category_id' => $gradeId, 'category' => 'transport'],
                ['amount' => $amounts['transport'] ?? 0, 'description' => 'Transport Fees', 'name' => 'Transport']
            );
            // Others
            FeeStructure::updateOrCreate(
                ['student_category_id' => $gradeId, 'category' => 'others'],
                ['amount' => $amounts['others'] ?? 0, 'description' => 'Other Charges', 'name' => 'Others']
            );
        }

        return back()->with('success', 'Fee structures updated successfully.');
    }
}
