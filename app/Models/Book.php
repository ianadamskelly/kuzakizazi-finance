<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'google_drive_id',
        'total_pages',
        'cover_image',
    ];

    public function getCoverUrlAttribute()
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : 'https://placehold.co/400x600/3b82f6/white?text=' . urlencode($this->title);
    }

    public function readers()
    {
        return $this->belongsToMany(User::class, 'reading_progress');
    }

    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'reading_progress')
            ->withPivot('current_page', 'percentage', 'is_completed')
            ->withTimestamps();
    }
}
