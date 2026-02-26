<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['name', 'next_grade_id'];

    public function nextGrade()
    {
        return $this->belongsTo(Grade::class, 'next_grade_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
