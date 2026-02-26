<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campus_id',
        'first_name',
        'last_name',
        'job_title',
        'employee_category_id', // Add this
        'bank_name',
        'bank_account_number',
        'bank_branch_code',
    ];

    /**
     * Get the user account for this employee.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the campus this employee belongs to.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the salary category for this employee.
     */
    public function employeeCategory()
    {
        return $this->belongsTo(EmployeeCategory::class);
    }

    /**
     * Get all payslips for this employee.
     */
    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }
}
