<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmployeeCategory;
use Illuminate\Http\Request;

class EmployeeCategoryController extends Controller
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
            'name' => 'required|string|max:255|unique:employee_categories,name',
            'base_salary' => 'required|numeric|min:0',
        ]);

        EmployeeCategory::create($request->all());

        return back()->with('success', 'Employee category created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeCategory $employeeCategory)
    {
        if ($employeeCategory->employees()->exists()) {
            return back()->with('error', 'Cannot delete category. It is currently assigned to employees.');
        }

        $employeeCategory->delete();

        return back()->with('success', 'Employee category deleted successfully.');
    }
}