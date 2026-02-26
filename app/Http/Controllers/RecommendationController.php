<?php

namespace App\Http\Controllers;

use App\Models\Recommendation;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'note' => 'nullable|string|max:255',
        ]);

        Recommendation::create([
            'teacher_id' => auth()->id(),
            'student_id' => $request->student_id,
            'book_id' => $request->book_id,
            'note' => $request->note,
        ]);

        return back()->with('status', 'Book recommended successfully!');
    }
}
