<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address'];

    /**
     * Get the employees for the campus.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the students for the campus.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
