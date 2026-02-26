<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    /**
     * Get the fee structures for this category.
     */
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    /**
     * Get all students in this category.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
