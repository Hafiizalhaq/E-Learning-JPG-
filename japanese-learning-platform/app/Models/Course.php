<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'short_description',
        'jlpt_level',
        'duration_hours',
        'price',
        'is_published',
        'is_free',
        'thumbnail_image',
    ];

    protected $casts = [
        'instructor_id' => 'integer',
        'category_id' => 'integer',
        'duration_hours' => 'integer',
        'price' => 'decimal:2',
        'is_published' => 'boolean',
        'is_free' => 'boolean',
    ];

    /**
     * Get the instructor who created this course
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the category of this course
     */
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * Get the modules of this course
     */
    public function modules()
    {
        return $this->hasMany(CourseModule::class, 'course_id');
    }

    /**
     * Get the enrollments of this course
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }

    /**
     * Get the students enrolled in this course
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'user_id')
                    ->withPivot('progress_percentage', 'is_completed', 'completed_at')
                    ->withTimestamps();
    }
}