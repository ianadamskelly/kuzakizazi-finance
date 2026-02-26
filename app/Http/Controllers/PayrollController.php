<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\PayslipItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function __construct()
    {
        // We can create a new 'manage payroll' permission later, for now we'll use 'manage expenses'
        $this->middleware('can:manage expenses');
    }

    /**
     * Display a listing of the generated payslips.
     */
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);
        $user = Auth::user();

        $payslipsQuery = Payslip::with(['employee.campus'])
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear);

        // --- Campus Filtering Logic ---
        if ($user->can('view all campuses')) {
            $selectedCampusId = session('selected_campus_id');
            if ($selectedCampusId && $selectedCampusId !== 'all') {
                // Filter by the selected campus for admins/directors
                $payslipsQuery->whereHas('employee', fn($q) => $q->where('campus_id', $selectedCampusId));
            }
        } else {
            // Force filter for users who can only see their own campus
            $payslipsQuery->whereHas('employee', fn($q) => $q->where('campus_id', $user->employee->campus_id));
        }

        $payslips = $payslipsQuery->latest()->paginate(15);

        return view('payroll.index', compact('payslips', 'selectedMonth', 'selectedYear'));
    }

    /**
     * Show the form for creating new payslips.
     */
    public function create()
    {
        return view('payroll.create');
    }

    /**
     * Generate and store new payslips for all employees.
     */
    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);

        $month = $request->input('month');
        $year = $request->input('year');

        // Find all employees with a salary category assigned
        $employees = Employee::whereHas('employeeCategory')->with('employeeCategory')->get();
        $payslipsGenerated = 0;

        DB::transaction(function () use ($employees, $month, $year, &$payslipsGenerated) {
            foreach ($employees as $employee) {
                // Check if a payslip for this month/year already exists for this employee
                $existingPayslip = Payslip::where('employee_id', $employee->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->exists();

                if (!$existingPayslip) {
                    $baseSalary = $employee->employeeCategory->base_salary;
                    Payslip::create([
                        'employee_id' => $employee->id,
                        'month' => $month,
                        'year' => $year,
                        'status' => 'unpaid',
                        'base_salary' => $baseSalary,
                        'total_earnings' => $baseSalary, // Initially, earnings are just the base salary
                        'total_deductions' => 0,
                        'net_pay' => $baseSalary,
                        'fund_source' => 'fees', // Default all salaries to 'fees' fund
                    ]);
                    $payslipsGenerated++;
                }
            }
        });

        return redirect()->route('payroll.index')->with('success', "$payslipsGenerated new payslips generated for " . date('F', mktime(0, 0, 0, $month, 1)) . " $year.");
    }

    /**
     * Display the specified payslip.
     */
    public function show(Payslip $payslip)
    {
        $payslip->load(['employee', 'items']);
        return view('payroll.show', compact('payslip'));
    }

    /**
     * Download a single payslip as a PDF.
     */
    public function downloadPdf(Payslip $payslip)
    {
        $payslip->load(['employee.campus', 'employee.employeeCategory', 'items']);
        $pdf = Pdf::loadView('payroll.pdf.payslip', compact('payslip'));
        return $pdf->download('payslip-' . $payslip->employee->first_name . '-' . $payslip->month . '-' . $payslip->year . '.pdf');
    }

    /**
     * Add an item (earning or deduction) to a payslip.
     */
    public function storeItem(Request $request, Payslip $payslip)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'amount' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request, $payslip) {
            $payslip->items()->create($request->all());

            // Recalculate totals
            $totalEarnings = $payslip->base_salary + $payslip->items()->where('type', 'earning')->sum('amount');
            $totalDeductions = $payslip->items()->where('type', 'deduction')->sum('amount');
            $netPay = $totalEarnings - $totalDeductions;

            $payslip->update([
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
            ]);
        });

        return back()->with('success', 'Payslip item added.');
    }

    /**
     * Remove an item from a payslip.
     */
    public function destroyItem(PayslipItem $item)
    {
        $payslip = $item->payslip;

        DB::transaction(function () use ($item, $payslip) {
            $item->delete();
            // Recalculate totals
            $totalEarnings = $payslip->base_salary + $payslip->items()->where('type', 'earning')->sum('amount');
            $totalDeductions = $payslip->items()->where('type', 'deduction')->sum('amount');
            $netPay = $totalEarnings - $totalDeductions;

            $payslip->update([
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
            ]);
        });

        return back()->with('success', 'Payslip item removed.');
    }

    /**
     * Mark a payslip as paid.
     */
    public function markAsPaid(Payslip $payslip)
    {
        $payslip->update(['status' => 'paid']);
        return back()->with('success', 'Payslip marked as paid.');
    }

    /**
     * Mark all unpaid payslips for a given month/year as paid.
     */
    public function bulkMarkAsPaid(Request $request)
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
        ]);

        $updatedCount = Payslip::where('month', $request->month)
            ->where('year', $request->year)
            ->where('status', 'unpaid')
            ->update(['status' => 'paid']);

        return back()->with('success', "$updatedCount payslips have been marked as paid.");
    }
}
