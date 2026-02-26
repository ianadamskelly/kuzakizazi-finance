<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
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
            'name' => 'required|string|max:255|unique:campuses,name',
            'address' => 'nullable|string|max:255',
        ]);

        Campus::create($request->all());

        return back()->with('success', 'Campus created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campus $campus)
    {
        return view('settings.campuses.edit', compact('campus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campus $campus)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campuses,name,' . $campus->id,
            'address' => 'nullable|string|max:255',
        ]);

        $campus->update($request->all());

        return redirect()->route('settings.index')->with('success', 'Campus updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campus $campus)
    {
        // Add checks to prevent deletion if linked to students or employees
        if ($campus->students()->exists() || $campus->employees()->exists()) {
            return back()->with('error', 'Cannot delete campus. It is currently assigned to students or employees.');
        }

        $campus->delete();

        return back()->with('success', 'Campus deleted successfully.');
    }
}