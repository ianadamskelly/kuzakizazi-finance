<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Updated fillable to match the EXISTING database columns
    protected $fillable = [
        'student_id',
        'category',
        'amount_paid',    // CORRECT: Matches DB
        'payment_method', // CORRECT: Matches DB
        'payment_date',   // CORRECT: Matches DB
        'reference_no',
        'received_by',
        'invoice_id'      // Optional, but keeping for compatibility
    ];

    /**
     * Define the relationship to the Student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Define the relationship to the Invoice.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}