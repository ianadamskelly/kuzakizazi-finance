<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'invoice_id',
        'payment_id',
        'category',
        'type',
        'amount',
        'balance_after_transaction',
        'description',
    ];
}