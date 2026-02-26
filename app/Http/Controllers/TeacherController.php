<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function dashboard(Request $request)
    {
        if (auth()->user()->role !== 'teacher') {
            return redirect()->route('student.dashboard');
        }

        $search = $request->input('search');

        $books = Book::when($search, function ($query, $search) {
            return $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('author', 'LIKE', "%{$search}%");
        })->get();

        $students = User::where('role', 'student')->get();

        return view('teacher.dashboard', compact('books', 'students'));
    }

    public function showStudentProgress(User $student)
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403);
        }

        $progress = $student->readingProgress()
            ->with('book')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('teacher.student-progress', compact('student', 'progress'));
    }
}
