<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayslipItem extends Model
{
    use HasFactory;

    protected $fillable = ['payslip_id', 'description', 'type', 'amount'];

    /**
     * Get the payslip this item belongs to.
     */
    public function payslip()
    {
        return $this->belongsTo(Payslip::class);
    }
}