<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Expense;

class SearchController extends Controller
{
    /**
     * Handle the incoming search request.
     */
    public function __invoke(Request $request)
    {
        $term = $request->input('query');
        $user = Auth::user();
        $results = collect(); // Use a collection to hold all results

        // --- Scope based on user permissions ---
        $selectedCampusId = session('selected_campus_id');
        $campusId = null;
        if ($user->can('view all campuses')) {
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $campusId = $selectedCampusId;
            }
        } else {
            $campusId = $user->employee->campus_id;
        }

        // --- Search Students ---
        $students = Student::with('campus')
            ->where(function ($query) use ($term) {
                $query->where('first_name', 'LIKE', "%{$term}%")
                      ->orWhere('last_name', 'LIKE', "%{$term}%")
                      ->orWhere('admission_number', 'LIKE', "%{$term}%");
            })
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->limit(5)
            ->get();
        
        foreach ($students as $student) {
            $results->push([
                'type' => 'Student',
                'title' => $student->first_name . ' ' . $student->last_name,
                'subtitle' => 'Admission No: ' . $student->admission_number,
                'url' => route('students.edit', $student)
            ]);
        }

        // --- Search Invoices ---
        // (Only if user has permission to view invoices)
        if ($user->can('manage fees')) {
            $invoices = Invoice::with('student')
                ->where('id', $term) // Search by invoice ID
                ->when($campusId, fn($q) => $q->whereHas('student', fn($subQ) => $subQ->where('campus_id', $campusId)))
                ->limit(5)
                ->get();

            foreach ($invoices as $invoice) {
                $results->push([
                    'type' => 'Invoice',
                    'title' => 'INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT),
                    'subtitle' => 'For: ' . $invoice->student->first_name . ' ' . $invoice->student->last_name,
                    'url' => route('invoices.show', $invoice)
                ]);
            }
        }

        // --- Search Expenses ---
        // (Only if user has permission to view expenses)
        if ($user->can('manage expenses')) {
            $expenses = Expense::where(function ($query) use ($term) {
                    $query->where('category', 'LIKE', "%{$term}%")
                          ->orWhere('vendor', 'LIKE', "%{$term}%");
                })
                ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
                ->limit(5)
                ->get();
            
            foreach ($expenses as $expense) {
                $results->push([
                    'type' => 'Expense',
                    'title' => $expense->category . ' ($' . number_format($expense->amount, 2) . ')',
                    'subtitle' => 'Vendor: ' . ($expense->vendor ?? 'N/A'),
                    'url' => route('expenses.edit', $expense)
                ]);
            }
        }

        return view('search.results', [
            'results' => $results,
            'searchTerm' => $term,
        ]);
    }
}
