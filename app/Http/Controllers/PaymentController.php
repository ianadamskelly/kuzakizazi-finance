<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage fees');
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(Request $request)
    {
        $student = null;
        if ($request->has('student_id')) {
            $student = Student::find($request->student_id);
        }
        $students = Student::all(); // For the dropdown if no student selected
        return view('payments.create', compact('student', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|in:fees,food,transport,others',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string|unique:payments,reference_no',
            'payment_date' => 'required|date',
            'invoice_id' => 'nullable|exists:invoices,id', // Add validation for optional invoice_id
        ]);

        $student = Student::findOrFail($request->student_id);

        return DB::transaction(function () use ($request, $student) {
            // 1. Create the Payment Record
            // Note: We use the fillable attributes defined in the Payment model.
            $paymentData = [
                'student_id' => $student->id,
                'amount_paid' => $request->amount,       // Map to 'amount_paid' in DB
                'category' => $request->category,
                'payment_method' => $request->payment_method, // Map to 'payment_method' in DB
                'reference_no' => $request->reference_no,
                'payment_date' => $request->payment_date,     // Map to 'payment_date' in DB
                'received_by' => auth()->id(),
            ];

            // If invoice_id is present (and model supports it), add it. 
            // Note: Payment model currently might not have invoice_id in fillable or DB based on previous migration history,
            // but we can certainly link it in the Ledger and update the Invoice status.
            if ($request->filled('invoice_id')) {
                $paymentData['invoice_id'] = $request->invoice_id; // Try to save if column exists
            }

            $payment = Payment::create($paymentData);

            // 2. CREDIT the Student Ledger for this specific category
            $ledgerEntry = $student->ledgers()->create([
                'category' => $request->category,
                'type' => 'credit',
                'amount' => $request->amount,
                // Balance decreases by payment amount
                'balance_after_transaction' => $student->balance - $request->amount,
                'description' => "Payment received for " . ucfirst($request->category),
                'payment_id' => $payment->id,
                'invoice_id' => $request->invoice_id, // Link to invoice in the ledger
            ]);

            // 3. Decrement the specific balance column
            $balanceField = "{$request->category}_balance";

            // Logic: Reducing the debt by the amount paid
            if (\Schema::hasColumn('students', $balanceField)) {
                $student->decrement($balanceField, $request->amount);
            }

            // Update main balance as well (Optional but recommended for syncing)
            $student->balance -= $request->amount;
            $student->save();

            // 4. Handle Specific Invoice logic if invoice_id is provided
            if ($request->filled('invoice_id')) {
                $invoice = \App\Models\Invoice::find($request->invoice_id);
                if ($invoice) {
                    $invoice->balance_due -= $request->amount;
                    if ($invoice->balance_due <= 0) {
                        $invoice->balance_due = 0; // Prevent negative due
                        $invoice->status = 'paid';
                    } else {
                        $invoice->status = 'partially_paid';
                    }
                    $invoice->save();
                }

                // Redirect back to invoice if came from there
                return redirect()->route('invoices.show', $invoice->id)
                    ->with('success', "Payment recorded successfully.");
            }

            return redirect()->route('students.show', $student->id)
                ->with('success', "Payment of " . number_format($request->amount, 2) . " credited to {$request->category}.");
        });
    }
}