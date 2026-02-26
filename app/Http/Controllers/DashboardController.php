<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Campus;
use App\Models\Expense;
use App\Models\Student;
use App\Models\Donation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Payslip;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $user = Auth::user();
        $campusId = session('selected_campus_id');

        // --- Collections Query ---
        $collectionsQuery = Payment::whereMonth('payment_date', $now->month)
            ->whereYear('payment_date', $now->year);

        // --- Outstanding (Debt) Query ---
        $studentsQuery = Student::query();

        // --- Expenses Query ---
        $expensesQuery = Expense::whereMonth('expense_date', $now->month)
            ->whereYear('expense_date', $now->year);

        // --- Payroll Query ---
        $payrollQuery = Payslip::where('month', $now->month)
            ->where('year', $now->year)
            ->where('status', 'paid');

        // Scope by Campus if necessary
        if ($campusId && $campusId !== 'all') {
            $collectionsQuery->whereHas('student', fn($q) => $q->where('campus_id', $campusId));
            $studentsQuery->where('campus_id', $campusId);
            $expensesQuery->where('campus_id', $campusId);
            $payrollQuery->whereHas('employee', fn($q) => $q->where('campus_id', $campusId));
        } elseif (!$user->can('view all campuses')) {
            $myCampusId = $user->employee->campus_id ?? null;
            $collectionsQuery->whereHas('student', fn($q) => $q->where('campus_id', $myCampusId));
            $studentsQuery->where('campus_id', $myCampusId);
            $expensesQuery->where('campus_id', $myCampusId);
            $payrollQuery->whereHas('employee', fn($q) => $q->where('campus_id', $myCampusId));
        }

        // 1. Get Monthly Collections per Category
        $monthlyCollections = $collectionsQuery
            ->select('category', DB::raw('SUM(amount_paid) as total'))
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category');

        // 2. Get Total Outstanding per Category (Source of Truth: Student Table)
        $outstanding = (object) [
            'tuition' => (float) $studentsQuery->clone()->sum('fees_balance'),
            'food' => (float) $studentsQuery->clone()->sum('food_balance'),
            'transport' => (float) $studentsQuery->clone()->sum('transport_balance'),
            'others' => (float) ($studentsQuery->clone()->sum('diaries_balance') +
                $studentsQuery->clone()->sum('assessment_balance') +
                $studentsQuery->clone()->sum('uniform_balance') +
                $studentsQuery->clone()->sum('others_balance')),
        ];

        // 3. Expense Summary
        $totalGeneralExpenses = (float) $expensesQuery->sum('amount');
        $totalSalaries = (float) $payrollQuery->sum('net_pay'); // Net pay is the actual cash outflow
        $tuitionCollected = (float) ($monthlyCollections['fees'] ?? 0);
        $totalExpenditure = $totalGeneralExpenses + $totalSalaries;
        $netBalance = $tuitionCollected - $totalExpenditure;

        $expenseSummary = (object) [
            'general' => $totalGeneralExpenses,
            'salaries' => $totalSalaries,
            'total_expenditure' => $totalExpenditure,
            'tuition_collected' => $tuitionCollected,
            'net_balance' => $netBalance,
        ];

        return view('dashboard', compact('monthlyCollections', 'outstanding', 'expenseSummary'));
    }

    /**
     * Switch the active campus for the user's session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchCampus(Request $request)
    {
        if (!Auth::user()->can('view all campuses')) {
            abort(403, 'You are not authorized to switch campuses.');
        }

        $request->validate([
            'campus_id' => 'required|in:all,' . Campus::pluck('id')->implode(','),
        ]);

        session(['selected_campus_id' => $request->campus_id]);

        return redirect()->route('dashboard');
    }
}