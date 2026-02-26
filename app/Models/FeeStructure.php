<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = ['student_category_id', 'description', 'amount', 'category', 'name', 'grade_id'];

    /**
     * Get the student category this fee structure belongs to.
     */
    public function studentCategory()
    {
        return $this->belongsTo(StudentCategory::class);
    }
}
