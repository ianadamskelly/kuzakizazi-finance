<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Payslip;
use Illuminate\Support\Facades\DB;

class GeneratePayroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payroll:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly payslips for all eligible employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payroll generation...');

        $month = now()->month;
        $year = now()->year;

        $employees = Employee::whereHas('employeeCategory')->with('employeeCategory')->get();
        $payslipsGenerated = 0;

        DB::transaction(function () use ($employees, $month, $year, &$payslipsGenerated) {
            foreach ($employees as $employee) {
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
                        'total_earnings' => $baseSalary,
                        'total_deductions' => 0,
                        'net_pay' => $baseSalary,
                    ]);
                    $payslipsGenerated++;
                }
            }
        });

        $this->info("Payroll generation complete. $payslipsGenerated new payslips were created for " . now()->format('F Y') . ".");
        return 0;
    }
}