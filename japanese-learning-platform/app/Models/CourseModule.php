<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published',
    ];

    protected $casts = [
        'course_id' => 'integer',
        'order' => 'integer',
        'is_published' => 'boolean',
    ];

    /**
     * Get the course this module belongs to
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the lessons in this module
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'module_id');
    }
}