<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Campus;
use App\Models\StudentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function __construct()
    {
        // Apply permissions to all methods in this controller
        $this->middleware('can:manage students');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Start building the query
        $studentsQuery = Student::with(['campus', 'studentCategory', 'grade']); // Load 'grade' relation

        // Scope the query based on user's permissions
        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $studentsQuery->where('campus_id', $selectedCampusId);
            }
        } else {
            // If user can only view their own campus, force the filter
            $studentsQuery->where('campus_id', $user->employee->campus_id);
        }

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        // Apply Grade Filter
        if ($request->filled('grade_id')) { // Changed to grade_id
            $studentsQuery->where('grade_id', $request->input('grade_id'));
        }

        // Get all grades for filter dropdown
        $grades = \App\Models\Grade::orderBy('name')->get();

        $students = $studentsQuery->latest()->paginate(10);

        return view('students.index', compact('students', 'grades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campuses = Campus::all();
        $studentCategories = StudentCategory::all();
        $grades = \App\Models\Grade::orderBy('name')->get(); // Fetch grades
        return view('students.create', compact('campuses', 'studentCategories', 'grades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'admission_number' => 'required|string|max:255|unique:students,admission_number',
            'grade_id' => 'required|exists:grades,id', // Validating grade_id
            'campus_id' => 'required|exists:campuses,id',
            'student_category_id' => 'required|exists:student_categories,id',
        ]);

        Student::create($request->all());

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $campuses = Campus::all();
        $studentCategories = StudentCategory::all();
        $grades = \App\Models\Grade::orderBy('name')->get();
        return view('students.edit', compact('student', 'campuses', 'studentCategories', 'grades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'admission_number' => 'required|string|max:255|unique:students,admission_number,' . $student->id,
            'grade_id' => 'required|exists:grades,id',
            'campus_id' => 'required|exists:campuses,id',
            'student_category_id' => 'required|exists:student_categories,id',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['campus', 'studentCategory', 'invoices', 'payments', 'ledgerEntries']);
        return view('students.show', compact('student'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        // Add a check here to prevent deletion if the student has invoices
        if ($student->invoices()->exists()) {
            return back()->with('error', 'Cannot delete student. They have existing invoices.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
    /**
     * Show the student's fee statement.
     */
    public function statement(Request $request, Student $student)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        // 1. Calculate Opening Balance: Sum of all transactions BEFORE start_date
        $openingBalanceDebits = $student->ledgerEntries()
            ->where('created_at', '<', $startDate . ' 00:00:00')
            ->where('type', 'debit')
            ->sum('amount');

        $openingBalanceCredits = $student->ledgerEntries()
            ->where('created_at', '<', $startDate . ' 00:00:00')
            ->where('type', 'credit')
            ->sum('amount');

        $openingBalance = $openingBalanceDebits - $openingBalanceCredits;

        // 2. Fetch Transactions within the period
        $transactions = $student->ledgerEntries()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Calculate Running Balances
        $runningBalance = $openingBalance;
        $statementData = $transactions->map(function ($transaction) use (&$runningBalance) {
            if ($transaction->type === 'debit') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }
            $transaction->running_balance = $runningBalance;
            return $transaction;
        });

        $closingBalance = $runningBalance;

        // Summary for top cards
        $totalDebits = $transactions->where('type', 'debit')->sum('amount');
        $totalCredits = $transactions->where('type', 'credit')->sum('amount');

        return view('students.statement', compact(
            'student',
            'startDate',
            'endDate',
            'statementData',
            'openingBalance',
            'closingBalance',
            'totalDebits',
            'totalCredits'
        ));
    }
}