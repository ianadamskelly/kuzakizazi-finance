<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'due_date', 'status', 'total_amount', 'balance_due'];

    /**
     * Get the student that owns the invoice.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get all of the items for the invoice.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get all of the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * When an invoice is created, we need to log the debits in the ledger.
     */
    protected static function booted()
    {
        static::created(function ($invoice) {
            // Note: Actual item categorization happens in the InvoiceController 
            // during bulk generation, which we will update next.
        });
    }
}
