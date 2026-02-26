<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = ['campus_id', 'donor_name', 'amount', 'donation_date'];

    /**
     * Get the campus the donation was for.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
