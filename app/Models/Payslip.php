<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'status',
        'base_salary',
        'total_earnings',
        'total_deductions',
        'net_pay',
        'fund_source',
    ];

    /**
     * Get the employee this payslip belongs to.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get all items for this payslip.
     */
    public function items()
    {
        return $this->hasMany(PayslipItem::class);
    }
}