<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:generate reports');
    }

    /**
     * Display the main reports hub page.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate and display the fee defaulters report.
     */
    public function feeDefaulters(Request $request)
    {
        $user = Auth::user();
        $gradeId = $request->input('grade_id');
        $grades = Grade::orderBy('name')->get();

        $invoicesQuery = Invoice::with(['student.campus', 'student.grade'])
            ->where('balance_due', '>', 0)
            ->whereIn('status', ['unpaid', 'partially_paid']);

        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                $invoicesQuery->whereHas('student', fn($q) => $q->where('campus_id', $selectedCampusId));
            }
        }
        else {
            $invoicesQuery->whereHas('student', fn($q) => $q->where('campus_id', $user->employee->campus_id));
        }

        // Apply grade filter
        if ($gradeId) {
            $invoicesQuery->whereHas('student', fn($q) => $q->where('grade_id', $gradeId));
        }

        $defaulters = $invoicesQuery->get()
            ->groupBy('student_id')
            ->map(function ($studentInvoices) {
            return [
            'student' => $studentInvoices->first()->student,
            'total_balance' => $studentInvoices->sum('balance_due'),
            'invoice_count' => $studentInvoices->count(),
            ];
        })->sortByDesc('total_balance');

        // Check if the request is for a PDF export
        if ($request->has('export')) {
            $selectedGrade = $gradeId ?Grade::find($gradeId) : null;
            $pdf = Pdf::loadView('reports.pdf.fee-defaulters', compact('defaulters', 'selectedGrade'));
            return $pdf->download('fee-defaulters-report-' . date('Y-m-d') . '.pdf');
        }

        return view('reports.fee-defaulters', compact('defaulters', 'grades', 'gradeId'));
    }
    /**
     * Generate and display the Income Statement.
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $user = Auth::user();
        $campusId = $this->getScopedCampusId($user);

        // Get total revenue (from payments) within the date range
        $revenue = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->when($campusId, fn($q) => $q->whereHas('student', fn($subQ) => $subQ->where('campus_id', $campusId)))
            ->sum('amount_paid');

        // Get expenses grouped by category within the date range
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('total');
        $netProfit = $revenue - $totalExpenses;

        // Check if the request is for a PDF export
        if ($request->has('export')) {
            $pdf = Pdf::loadView('reports.pdf.income-statement', compact('revenue', 'expenses', 'totalExpenses', 'netProfit', 'startDate', 'endDate'));
            return $pdf->download('income-statement-' . $startDate . '-to-' . $endDate . '.pdf');
        }

        return view('reports.income-statement', compact('revenue', 'expenses', 'totalExpenses', 'netProfit', 'startDate', 'endDate'));
    }

    /**
     * Generate and display the Expense Report.
     */
    public function expenseReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $user = Auth::user();
        $campusId = $this->getScopedCampusId($user);

        $expenses = Expense::with('campus')->whereBetween('expense_date', [$startDate, $endDate])
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->orderBy('expense_date', 'desc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        if ($request->has('export')) {
            $pdf = Pdf::loadView('reports.pdf.expense-report', compact('expenses', 'totalExpenses', 'startDate', 'endDate'));
            return $pdf->download('expense-report-' . $startDate . '-to-' . $endDate . '.pdf');
        }

        return view('reports.expense-report', compact('expenses', 'totalExpenses', 'startDate', 'endDate'));
    }

    /**
     * Helper function to determine the campus ID scope.
     */
    private function getScopedCampusId($user)
    {
        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            return ($selectedCampusId && $selectedCampusId !== 'all') ? $selectedCampusId : null;
        }
        return $user->employee->campus_id;
    }

    /**
     * Generate and display the Fund Balance Report.
     */
    public function fundBalance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $user = Auth::user();
        $campusId = $this->getScopedCampusId($user);

        // Define valid funds mapping
        $funds = [
            'fees' => 'Tuition Fees',
            'food' => 'Canteen / Food',
            'transport' => 'Transport',
            'others' => 'Other Collections'
        ];

        // 1. Calculate Income (Total Payments per Category)
        $incomeData = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->when($campusId, fn($q) => $q->whereHas('student', fn($subQ) => $subQ->where('campus_id', $campusId)))
            ->select('category', DB::raw('SUM(amount_paid) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // 2. Calculate Expenses (Expenses + Paid Payslips)
        // A. Direct Expenses from Expenses table
        $directExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->select('fund_source', DB::raw('SUM(amount) as total'))
            ->groupBy('fund_source')
            ->pluck('total', 'fund_source')
            ->toArray();

        // B. Payroll Expenses (Paid Payslips)
        // Note: Payslips have 'month' and 'year', not a specific date, so we approximate or check if the Payslip month/year falls in range.
        // For accurate cash flow, we should rely on a 'payment_date' if we had one for payslips.
        // For now, we'll assume the payslip 'created_at' or we just filter by month/year matching the range roughly.
        // Better approach: User start/end date logic to filtering month/year columns.

        $startYear = substr($startDate, 0, 4);
        $startMonth = substr($startDate, 5, 2);
        $endYear = substr($endDate, 0, 4);
        $endMonth = substr($endDate, 5, 2);

        // Simple approximate query: Payslips where status is paid
        // We will just filter 'paid' payslips created within the timeframe for simplicity
        $payrollExpenses = \App\Models\Payslip::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate]) // Using creation date as proxy for payment date
            ->when($campusId, fn($q) => $q->whereHas('employee', fn($subQ) => $subQ->where('campus_id', $campusId)))
            ->select('fund_source', DB::raw('SUM(net_pay) as total'))
            ->groupBy('fund_source')
            ->pluck('total', 'fund_source')
            ->toArray();

        // 3. Aggregate Data
        $fundBalances = [];
        foreach ($funds as $key => $label) {
            $income = $incomeData[$key] ?? 0;
            $expense = ($directExpenses[$key] ?? 0) + ($payrollExpenses[$key] ?? 0);

            $fundBalances[$key] = [
                'label' => $label,
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense
            ];
        }

        if ($request->has('export')) {
            $pdf = Pdf::loadView('reports.pdf.fund-balance', compact('fundBalances', 'startDate', 'endDate'));
            return $pdf->download('fund-balance-' . $startDate . '-to-' . $endDate . '.pdf');
        }

        return view('reports.fund-balance', compact('fundBalances', 'startDate', 'endDate'));
    }
}
