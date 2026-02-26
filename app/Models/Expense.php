<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id', 'category', 'amount', 'fund_source', 'vendor', 'expense_date', 'receipt_url', 'receipt_path'];

    /**
     * Get the campus the expense belongs to.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
