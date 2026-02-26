<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'base_salary'];

    /**
     * Get the employees in this category.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}