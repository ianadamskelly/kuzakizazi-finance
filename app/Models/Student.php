<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'campus_id',
        'student_category_id',
        'grade_id', // Replaces 'grade'
        'status',   // New column
        'first_name',
        'last_name',
        // 'grade', // Removed
        'admission_number',
        'balance',
        'fees_balance',
        'food_balance',
        'diaries_balance',
        'assessment_balance',
        'uniform_balance',
        'transport_balance',
        'others_balance',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function studentCategory()
    {
        return $this->belongsTo(StudentCategory::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get all invoices for the student.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all payments for the student.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all ledger entries for the student.
     */
    public function ledgerEntries()
    {
        return $this->hasMany(StudentLedger::class)->latest();
    }

    /**
     * Get all ledger entries for the student (Limit scope/Alias).
     */
    public function ledgers()
    {
        return $this->hasMany(StudentLedger::class);
    }
}