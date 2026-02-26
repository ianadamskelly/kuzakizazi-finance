<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\StudentLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function __construct()
    {
        // Apply permissions to all methods in this controller
        $this->middleware('can:manage fees');
    }

    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $invoicesQuery = Invoice::with(['student.campus']);

        // --- FILTERING LOGIC ---

        // Filter by search term (student name or admission number)
        $invoicesQuery->when($request->search, function ($query, $search) {
            $query->whereHas('student', function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_number', 'like', "%{$search}%");
            });
        });

        // Filter by grade
        $invoicesQuery->when($request->grade_id, function ($query, $gradeId) {
            $query->whereHas('student', fn($q) => $q->where('grade_id', $gradeId));
        });

        // Filter by status
        $invoicesQuery->when($request->status, function ($query, $status) {
            $query->where('status', $status);
        });

        // Scope the query based on user's permissions
        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $invoicesQuery->whereHas('student', fn($q) => $q->where('campus_id', $selectedCampusId));
            }
        } else {
            $invoicesQuery->whereHas('student', fn($q) => $q->where('campus_id', $user->employee->campus_id));
        }

        $invoices = $invoicesQuery->latest()->paginate(15)->withQueryString();

        // Get unique grades for the filter dropdown
        $grades = \App\Models\Grade::orderBy('name')->get();

        return view('invoices.index', [
            'invoices' => $invoices,
            'grades' => $grades,
            'filters' => $request->only(['search', 'grade_id', 'status']), // Pass filters back to the view
        ]);
    }

    /**
     * Show the form for generating new invoices.
     */
    public function create()
    {
        $user = Auth::user();
        $studentsQuery = Student::active()->with('campus', 'grade', 'studentCategory');

        // Scope by Campus if necessary
        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $studentsQuery->where('campus_id', $selectedCampusId);
            }
        } else {
            $myCampusId = $user->employee->campus_id ?? null;
            $studentsQuery->where('campus_id', $myCampusId);
        }

        $students = $studentsQuery->orderBy('first_name')->get();

        return view('invoices.create', compact('students'));
    }

    /**
     * Generate and store new invoices for all students (OLD LOGIC).
     * Kept for compatibility with existing UI.
     */
    /**
     * Store a newly created invoice in storage (Single Charge).
     */
    public function store(Request $request)
    {
        // 1. Check if this is a Bulk Generation or a Single Charge
        if (!$request->has('student_id')) {
            return $this->handleBulkStore($request);
        }

        // --- SINGLE MANUAL CHARGE LOGIC (Current logic) ---
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|in:fees,food,transport,others',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $student = Student::findOrFail($request->student_id);

        DB::transaction(function () use ($request, $student) {
            // 1. Create the Invoice
            $invoice = Invoice::create([
                'student_id' => $student->id,
                'campus_id' => $student->campus_id,
                'total_amount' => $request->amount,
                'balance_due' => $request->amount,
                'due_date' => $request->due_date,
                'term' => 'Ad-hoc', // Or derive from current term
                'status' => 'unpaid',
            ]);

            // 2. Create the Invoice Item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => ucfirst($request->category) . ' Charge',
                'amount' => $request->amount,
            ]);

            // 3. Increment the specific balance column
            $balanceField = "{$request->category}_balance";
            if (\Schema::hasColumn('students', $balanceField)) {
                Student::where('id', $student->id)->increment($balanceField, $request->amount);
            }

            // 4. Increment the main total balance
            Student::where('id', $student->id)->increment('balance', $request->amount);
            $student->refresh();

            // 5. Create Ledger Entry
            StudentLedger::create([
                'student_id' => $student->id,
                'invoice_id' => $invoice->id,
                'category' => $request->category,
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after_transaction' => $student->balance,
                'description' => $request->description ?: "Manual Charge for " . ucfirst($request->category),
            ]);
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice generated successfully.');
    }

    /**
     * Handle global bulk invoice generation for all active students.
     */
    protected function handleBulkStore(Request $request)
    {
        $request->validate([
            'due_date' => 'required|date',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $query = Student::active();

        if ($request->has('student_ids') && !empty($request->student_ids)) {
            $query->whereIn('id', $request->student_ids);
        }

        $students = $query->get();
        $count = 0;

        foreach ($students as $student) {
            // Get fee structure for this student category
            $fees = FeeStructure::where('student_category_id', $student->student_category_id)
                ->get();

            if ($fees->isEmpty()) {
                continue;
            }

            DB::transaction(function () use ($student, $fees, $request, &$count) {
                $totalAmount = $fees->sum('amount');

                // Create the Invoice
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'campus_id' => $student->campus_id,
                    'total_amount' => $totalAmount,
                    'balance_due' => $totalAmount, // Initialize balance_due
                    'due_date' => $request->due_date,
                    'term' => 'Current Term', // This could be dynamic based on settings
                    'status' => 'unpaid',
                ]);

                foreach ($fees as $fee) {
                    // Create Invoice Item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $fee->name,
                        'amount' => $fee->amount,
                    ]);

                    // Update Student Balances
                    $balanceField = "{$fee->category}_balance";
                    if (\Schema::hasColumn('students', $balanceField)) {
                        Student::where('id', $student->id)->increment($balanceField, $fee->amount);
                    }
                    Student::where('id', $student->id)->increment('balance', $fee->amount);

                    // Create Ledger Entry
                    $student = Student::find($student->id); // Re-fetch to ensure we have a fresh model for refresh/ledger
                    StudentLedger::create([
                        'student_id' => $student->id,
                        'invoice_id' => $invoice->id,
                        'category' => $fee->category,
                        'type' => 'debit',
                        'amount' => $fee->amount,
                        'balance_after_transaction' => $student->balance,
                        'description' => "Invoiced for {$fee->name}",
                    ]);
                }
                $count++;
            });
        }

        return redirect()->route('invoices.index')->with('success', "Invoices generated for {$count} students.");
    }

    /**
     * Generate bulk invoices for a specific Grade/Campus
     * This logic handles the multi-fund "Forking" of balances.
     * Use this method for the NEW bulk generation feature.
     */
    public function generateBulk(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'grade_id' => 'required|exists:grades,id',
            'term' => 'required|string',
            'due_date' => 'required|date',
        ]);

        // 1. Get all active students in the target group
        $students = Student::where('campus_id', $request->campus_id)
            ->where('grade_id', $request->grade_id)
            ->where('status', 'active')
            ->get();

        // 2. Get the fee structure for this grade
        $fees = FeeStructure::where('grade_id', $request->grade_id)->get();

        if ($fees->isEmpty()) {
            return back()->with('error', 'No fee structure found for this grade.');
        }

        $count = 0;

        foreach ($students as $student) {
            DB::transaction(function () use ($student, $fees, $request, &$count) {
                // 3. Create the Main Invoice
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'campus_id' => $request->campus_id,
                    'total_amount' => $fees->sum('amount'),
                    'due_date' => $request->due_date,
                    'term' => $request->term,
                    'status' => 'unpaid',
                ]);

                foreach ($fees as $fee) {
                    // 4. Create the Invoice Item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'name' => $fee->name,
                        'amount' => $fee->amount,
                        'category' => $fee->category, // Ensure your fee_structures table has a 'category' column
                    ]);

                    // 5. DEBIT the Student Ledger (The "Fork" logic)
                    // This records exactly which fund the student owes money to

                    // Note: In the new multi-fund system, "balance_after_transaction" is tricky because we have multiple balances.
                    // For now, we will store the TOTAL student balance after this transaction.
                    // We increment the balance column first so we can record the accurate new total.

                    // 6. Increment the specific balance column on the student record
                    $balanceField = "{$fee->category}_balance";

                    // Check if column exists to avoid errors during development
                    if (\Schema::hasColumn('students', $balanceField)) {
                        Student::where('id', $student->id)->increment($balanceField, $fee->amount);
                    }
                    // Also increment the main total balance
                    Student::where('id', $student->id)->increment('balance', $fee->amount);

                    // Refresh student to get updated balances
                    $student = Student::find($student->id);

                    $student->ledgers()->create([
                        'category' => $fee->category,
                        'type' => 'debit',
                        'amount' => $fee->amount,
                        'balance_after_transaction' => $student->balance, // Main total balance
                        'description' => "Invoiced for {$fee->name} ({$request->term})",
                        'invoice_id' => $invoice->id,
                    ]);
                }
                $count++;
            });
        }

        return redirect()->back()->with('success', "Successfully generated {$count} invoices.");
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['student.campus', 'items', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $this->authorize('manage fees');

        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
        ]);

        $invoiceIds = $request->input('invoice_ids');
        $invoices = Invoice::with('student')->whereIn('id', $invoiceIds)->get();

        DB::transaction(function () use ($invoices) {
            foreach ($invoices as $invoice) {
                $student = $invoice->student;
                $invoiceAmount = $invoice->total_amount;

                $student->balance -= $invoiceAmount;
                $student->save();

                $student->ledgerEntries()->create([
                    'invoice_id' => $invoice->id,
                    'type' => 'credit',
                    'amount' => $invoiceAmount,
                    'balance_after_transaction' => $student->balance,
                    'description' => 'Reversal for deleted Invoice INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT),
                ]);
            }

            // Delete all selected invoices at once
            Invoice::whereIn('id', $invoices->pluck('id'))->delete();
        });

        return redirect()->route('invoices.index')->with('success', count($invoices) . ' invoices deleted successfully and student balances adjusted.');
    }
}