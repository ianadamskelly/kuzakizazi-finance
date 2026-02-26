<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class GenerateInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly invoices for all students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting invoice generation...');

        $dueDate = now()->addDays(15)->format('Y-m-d'); // Set due date 15 days from now
        $students = Student::with('studentCategory.feeStructures')->get();
        $invoicesCreated = 0;

        DB::transaction(function () use ($students, $dueDate, &$invoicesCreated) {
            foreach ($students as $student) {
                if ($student->studentCategory?->feeStructures->isEmpty()) {
                    continue;
                }

                $totalAmount = $student->studentCategory->feeStructures->sum('amount');

                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'due_date' => $dueDate,
                    'status' => 'unpaid',
                    'total_amount' => $totalAmount,
                    'balance_due' => $totalAmount,
                ]);

                foreach ($student->studentCategory->feeStructures as $fee) {
                    $invoice->items()->create([
                        'description' => $fee->description,
                        'amount' => $fee->amount,
                    ]);
                }
                $invoicesCreated++;
            }
        });

        $this->info("Invoice generation complete. $invoicesCreated invoices were created.");
        return 0;
    }
}
